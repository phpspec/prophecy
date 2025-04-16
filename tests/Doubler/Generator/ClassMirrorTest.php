<?php

namespace Tests\Prophecy\Doubler\Generator;

use Fixtures\Prophecy\SelfReferencing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\ObjectType;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\ClassMirrorException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Prophet;

class ClassMirrorTest extends TestCase
{
    #[Test]
    public function it_reflects_allowed_magic_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\SpecialMethods');

        $mirror = new ClassMirror();

        $node = $mirror->reflect($class, array());

        $this->assertCount(7, $node->getMethods());
    }

    #[Test]
    public function it_reflects_protected_abstract_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithProtectedAbstractMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertEquals('Fixtures\Prophecy\WithProtectedAbstractMethod', $classNode->getParentClass());

        $methodNodes = $classNode->getMethods();
        $this->assertCount(1, $methodNodes);

        $this->assertEquals('protected', $methodNodes['innerDetail']->getVisibility());
    }

    #[Test]
    public function it_reflects_public_static_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithStaticMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertEquals('Fixtures\Prophecy\WithStaticMethod', $classNode->getParentClass());

        $methodNodes = $classNode->getMethods();
        $this->assertCount(1, $methodNodes);

        $this->assertTrue($methodNodes['innerDetail']->isStatic());
    }

    #[Test]
    public function it_marks_required_args_without_types_as_not_optional(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithArguments');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithoutTypeHints');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(1, $argNodes);

        $this->assertEquals('arg', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode(), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertNull($argNodes[0]->getDefault());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertFalse($argNodes[0]->isVariadic());
    }

    #[Test]
    public function it_properly_reads_methods_arguments_with_types(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithArguments');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(3, $argNodes);


        $this->assertEquals('arg_1', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode(new ObjectType('ArrayAccess')), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());

        $this->assertEquals('arg_2', $argNodes[1]->getName());
        $this->assertEquals(new ArgumentTypeNode(new BuiltinType('array')), $argNodes[1]->getTypeNode());
        $this->assertTrue($argNodes[1]->isOptional());
        $this->assertEquals(array(), $argNodes[1]->getDefault());
        $this->assertFalse($argNodes[1]->isPassedByReference());
        $this->assertFalse($argNodes[1]->isVariadic());

        $this->assertEquals('arg_3', $argNodes[2]->getName());
        $this->assertEquals(new ArgumentTypeNode(new UnionType([
            new BuiltinType('null'),
            new ObjectType('ArrayAccess'),
        ])), $argNodes[2]->getTypeNode());
        $this->assertTrue($argNodes[2]->isOptional());
        $this->assertNull($argNodes[2]->getDefault());
        $this->assertFalse($argNodes[2]->isPassedByReference());
        $this->assertFalse($argNodes[2]->isVariadic());
    }

    #[Test]
    public function it_properly_reads_methods_arguments_with_callable_types(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithCallableArgument');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(2, $argNodes);

        $this->assertEquals('arg_1', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode(new BuiltinType('callable')), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertFalse($argNodes[0]->isVariadic());

        $this->assertEquals('arg_2', $argNodes[1]->getName());
        $this->assertEquals(new ArgumentTypeNode(new UnionType([
            new BuiltinType('null'),
            new BuiltinType('callable'),
        ])), $argNodes[1]->getTypeNode());
        $this->assertTrue($argNodes[1]->isOptional());
        $this->assertNull($argNodes[1]->getDefault());
        $this->assertFalse($argNodes[1]->isPassedByReference());
        $this->assertFalse($argNodes[1]->isVariadic());
    }

    #[Test]
    public function it_properly_reads_methods_variadic_arguments(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithVariadicArgument');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(1, $argNodes);

        $this->assertEquals('args', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode(), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertTrue($argNodes[0]->isVariadic());
    }

    #[Test]
    public function it_properly_reads_methods_typehinted_variadic_arguments(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithTypehintedVariadicArgument');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithTypeHintedArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(1, $argNodes);

        $this->assertEquals('args', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode(new BuiltinType('array')), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertTrue($argNodes[0]->isVariadic());
    }

    #[Test]
    public function it_marks_passed_by_reference_args_as_passed_by_reference(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithReferences');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertTrue($classNode->hasMethod('methodWithReferenceArgument'));

        $argNodes = $classNode->getMethod('methodWithReferenceArgument')->getArguments();

        $this->assertCount(2, $argNodes);

        $this->assertTrue($argNodes[0]->isPassedByReference());
        $this->assertTrue($argNodes[1]->isPassedByReference());
    }

    #[Test]
    public function it_throws_an_exception_if_class_is_final(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\FinalClass');

        $mirror = new ClassMirror();

        $this->expectException(ClassMirrorException::class);

        $mirror->reflect($class, array());
    }

    #[Test]
    public function it_ignores_final_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(0, $classNode->getMethods());
    }

    #[Test]
    public function it_marks_final_methods_as_unextendable(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(1, $classNode->getUnextendableMethods());
        $this->assertFalse($classNode->isExtendable('finalImplementation'));
    }

    #[Test]
    public function it_throws_an_exception_if_interface_provided_instead_of_class(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\EmptyInterface');

        $mirror = new ClassMirror();

        $this->expectException(InvalidArgumentException::class);

        $mirror->reflect($class, array());
    }

    #[Test]
    public function it_reflects_all_interfaces_methods(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(null, array(
            new \ReflectionClass('Fixtures\Prophecy\Named'),
            new \ReflectionClass('Fixtures\Prophecy\ModifierInterface'),
        ));

        $this->assertEquals('stdClass', $classNode->getParentClass());
        $this->assertEquals(array(
            'Prophecy\Doubler\Generator\ReflectionInterface',
            'Fixtures\Prophecy\ModifierInterface',
            'Fixtures\Prophecy\Named',
        ), $classNode->getInterfaces());

        $this->assertCount(3, $classNode->getMethods());
        $this->assertTrue($classNode->hasMethod('getName'));
        $this->assertTrue($classNode->hasMethod('isAbstract'));
        $this->assertTrue($classNode->hasMethod('getVisibility'));
    }

    #[Test]
    public function it_ignores_virtually_private_methods(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithVirtuallyPrivateMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(2, $classNode->getMethods());
        $this->assertTrue($classNode->hasMethod('isAbstract'));
        $this->assertTrue($classNode->hasMethod('__toString'));
        $this->assertFalse($classNode->hasMethod('_getName'));
    }

    #[Test]
    public function it_does_not_throw_exception_for_virtually_private_finals(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalVirtuallyPrivateMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(0, $classNode->getMethods());
    }

    #[Test]
    public function it_reflects_return_typehints(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithReturnTypehints');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(3, $classNode->getMethods());
        $this->assertTrue($classNode->hasMethod('getName'));
        $this->assertTrue($classNode->hasMethod('getSelf'));
        $this->assertTrue($classNode->hasMethod('getParent'));

        $this->assertEquals(new ReturnTypeNode(new BuiltinType('string')), $classNode->getMethod('getName')->getReturnTypeNode());
        $this->assertEquals(new ReturnTypeNode(new ObjectType('Fixtures\Prophecy\WithReturnTypehints')), $classNode->getMethod('getSelf')->getReturnTypeNode());
        $this->assertEquals(new ReturnTypeNode(new ObjectType('Fixtures\Prophecy\EmptyClass')), $classNode->getMethod('getParent')->getReturnTypeNode());
    }

    #[Test]
    public function it_throws_an_exception_if_class_provided_in_interfaces_list(): void
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\EmptyClass');

        $mirror = new ClassMirror();

        $this->expectException(\InvalidArgumentException::class);

        $mirror->reflect(null, array($class));
    }

    #[Test]
    public function it_throws_an_exception_if_not_reflection_provided_as_interface(): void
    {
        $mirror = new ClassMirror();

        $this->expectException(\InvalidArgumentException::class);

        $mirror->reflect(null, array(null));
    }

    #[Test]
    public function it_doesnt_fail_to_typehint_nonexistent_FQCN(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\OptionalDepsClass'), array());
        $method = $classNode->getMethod('iHaveAStrangeTypeHintedArg');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode('I\Simply\Am\Nonexistent'), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_doesnt_fail_on_array_nullable_parameter_with_not_null_default_value(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableArrayParameter'), array());
        $method = $classNode->getMethod('iHaveNullableArrayParameterWithNotNullDefaultValue');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode(new UnionType([
            new BuiltinType('null'),
            new BuiltinType('array'),
        ])), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_doesnt_fail_to_typehint_nonexistent_RQCN(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\OptionalDepsClass'), array());
        $method = $classNode->getMethod('iHaveAnEvenStrangerTypeHintedArg');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode('I\Simply\Am\Not'), $arguments[0]->getTypeNode());
    }

    #[Test]
    function it_doesnt_fail_when_method_is_extended_with_more_params(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(
            new \ReflectionClass('Fixtures\Prophecy\MethodWithAdditionalParam'),
            array(new \ReflectionClass('Fixtures\Prophecy\Named'))
        );
        $method = $classNode->getMethod('getName');
        $this->assertCount(1, $method->getArguments());

        $method = $classNode->getMethod('methodWithoutTypeHints');
        $this->assertCount(2, $method->getArguments());
    }

    #[Test]
    function it_doesnt_fail_to_mock_self_referencing_interface(): void
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(null, array(new \ReflectionClass(SelfReferencing::class)));

        $method = $classNode->getMethod('__invoke');
        $this->assertCount(1, $method->getArguments());

        $this->assertEquals(new ArgumentTypeNode(SelfReferencing::class), $method->getArguments()[0]->getTypeNode());

        $this->assertEquals(new ReturnTypeNode(SelfReferencing::class), $method->getReturnTypeNode());
    }

    #[Test]
    function it_changes_argument_names_if_they_are_varying(): void
    {
        // Use test doubles in this test, as arguments named ... in the Reflection API can only happen for internal classes
        $prophet = new Prophet();
        $class = $prophet->prophesize('ReflectionClass');
        $method = $prophet->prophesize('ReflectionMethod');
        $parameter = $prophet->prophesize('ReflectionParameter');

        if (PHP_VERSION_ID >= 80200) {
            $class->isReadOnly()->willReturn(false);
        }
        $class->getName()->willReturn('Custom\ClassName');
        $class->isInterface()->willReturn(false);
        $class->isFinal()->willReturn(false);
        $class->getMethods(\ReflectionMethod::IS_PUBLIC)->willReturn(array($method));
        $class->getMethods(\ReflectionMethod::IS_ABSTRACT)->willReturn(array());

        $method->getParameters()->willReturn(array($parameter));
        $method->getName()->willReturn('methodName');
        $method->isFinal()->willReturn(false);
        $method->isProtected()->willReturn(false);
        $method->isStatic()->willReturn(false);
        $method->returnsReference()->willReturn(false);
        $method->hasReturnType()->willReturn(false);
        $method->getDeclaringClass()->willReturn($class);

        if (\PHP_VERSION_ID >= 80100) {
            $method->hasTentativeReturnType()->willReturn(false);
        }

        $parameter->getName()->willReturn('...');
        $parameter->isDefaultValueAvailable()->willReturn(true);
        $parameter->getDefaultValue()->willReturn(null);
        $parameter->isPassedByReference()->willReturn(false);
        $parameter->allowsNull()->willReturn(true);
        $parameter->getClass()->willReturn($class);
        $parameter->getType()->willReturn(null);
        $parameter->hasType()->willReturn(false);
        $parameter->isVariadic()->willReturn(false);

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class->reveal(), array());

        $methodNodes = $classNode->getMethods();

        $argumentNodes = $methodNodes['methodName']->getArguments();
        $argumentNode = $argumentNodes[0];

        $this->assertEquals('__dot_dot_dot__', $argumentNode->getName());
    }

    #[Test]
    public function it_can_double_a_class_with_union_return_types(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionReturnTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];


        $this->assertEquals(new UnionType([
            new ObjectType('stdClass'),
            new BuiltinType('bool'),
        ]), $methodNode->getReturnTypeNode()->getType());
    }

    #[Test]
    public function it_can_double_a_class_with_union_return_type_with_false(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types with false are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionReturnTypeFalse'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new UnionType([
            new ObjectType('stdClass'),
            new BuiltinType('false'),
        ]), $methodNode->getReturnTypeNode()->getType());
    }

    #[Test]
    public function it_can_double_a_class_with_union_argument_types(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionArgumentTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertEquals(new ArgumentTypeNode(\stdClass::class, 'bool'), $methodNode->getArguments()[0]->getTypeNode());
    }

    #[Test]
    public function it_can_double_a_class_with_union_argument_type_with_false(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types with false are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionArgumentTypeFalse'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ArgumentTypeNode(\stdClass::class, 'false'), $methodNode->getArguments()[0]->getTypeNode());
    }

    #[Test]
    public function it_can_double_a_class_with_mixed_types(): void
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Mixed type is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\MixedTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertEquals(new ArgumentTypeNode('mixed'), $methodNode->getArguments()[0]->getTypeNode());
        $this->assertEquals(new ReturnTypeNode('mixed'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_inherited_self_return_type(): void
    {
        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\ClassExtendAbstractWithMethodWithReturnType'), []);
        $methodNode = $classNode->getMethods()['returnSelf'];

        $this->assertEquals(new ReturnTypeNode('Fixtures\Prophecy\AbstractBaseClassWithMethodWithReturnType'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_never_return_type(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Never type is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\NeverType'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertEquals(new ReturnTypeNode('never'), $methodNode->getReturnTypeNode());

    }

    #[Test]
    public function it_can_not_double_an_enum(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Enums are not supported in this PHP version');
        }

        $this->expectException(ClassMirrorException::class);

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\Enum'), []);
    }

    #[Test]
    public function it_can_double_intersection_return_types(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Intersection types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\IntersectionReturnType'), []);

        $method = $classNode->getMethod('doSomething');
        $returnType = $method->getReturnTypeNode();

        $this->assertEquals(
            new IntersectionType([
                new ObjectType('Fixtures\Prophecy\Bar'),
                new ObjectType('Fixtures\Prophecy\Baz'),
            ]),
            $returnType->getType()
        );
    }

    #[Test]
    public function it_can_double_intersection_argument_types(): void
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('Intersection types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\IntersectionArgumentType'), []);

        $method = $classNode->getMethod('doSomething');
        $argType = $method->getArguments()[0]->getTypeNode();

        $this->assertEquals(
            new IntersectionType([
                new ObjectType('Fixtures\Prophecy\Bar'),
                new ObjectType('Fixtures\Prophecy\Baz'),
            ]),
            $argType->getType()
        );
    }

    #[Test]
    public function it_can_double_a_standalone_return_type_of_false(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone return type of false is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneReturnTypeFalse'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ReturnTypeNode('false'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_a_standalone_parameter_type_of_false(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone parameter type of false is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneParameterTypeFalse'), []);
        $method = $classNode->getMethod('method');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode('false'), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_can_double_a_nullable_return_type_of_false(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Nullable return type of false is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableReturnTypeFalse'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ReturnTypeNode('null', 'false'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_a_nullable_parameter_type_of_false(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Nullable parameter type of false is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableParameterTypeFalse'), []);
        $method = $classNode->getMethod('method');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode(new UnionType([
            new BuiltinType('null'),
            new BuiltinType('false'),
        ])), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_can_not_double_dnf_intersection_argument_types(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('DNF intersection types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\DnfArgumentType'), []);


        $method = $classNode->getMethod('doSomething');
        $argType = $method->getArguments()[0]->getTypeNode();

        $this->assertEquals(
            new UnionType([
                new IntersectionType([
                    new ObjectType('Fixtures\Prophecy\A'),
                    new ObjectType('Fixtures\Prophecy\B'),
                ]),
                new ObjectType('Fixtures\Prophecy\C'),
            ]),
            $argType->getType()
        );
    }

    #[Test]
    public function it_can_double_dnf_intersection_return_types(): void
    {
        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\DnfReturnType'), []);

        $method = $classNode->getMethod('doSomething');
        $returnType = $method->getReturnTypeNode();

        $this->assertEquals(
            new UnionType([
                new IntersectionType([
                    new ObjectType('Fixtures\Prophecy\A'),
                    new ObjectType('Fixtures\Prophecy\B'),
                ]),
                new ObjectType('Fixtures\Prophecy\C'),
            ]),
            $returnType->getType()
        );
    }

    #[Test]
    public function it_can_double_a_standalone_return_type_of_true(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone return type of true is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneReturnTypeTrue'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ReturnTypeNode('true'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_reflects_non_read_only_class(): void
    {
        $classNode = (new ClassMirror())->reflect(
            new \ReflectionClass('Fixtures\Prophecy\EmptyClass'),
            []
        );

        $this->assertFalse($classNode->isReadOnly());
    }

    #[Test]
    public function it_can_double_a_standalone_parameter_type_of_true(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone parameter type of true is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneParameterTypeTrue'), []);
        $method = $classNode->getMethod('method');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode('true'), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_can_double_a_nullable_return_type_of_true(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Nullable return type of true is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableReturnTypeTrue'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ReturnTypeNode('null', 'true'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_a_nullable_parameter_type_of_true(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Nullable parameter type of true is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableParameterTypeTrue'), []);
        $method = $classNode->getMethod('method');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode(new UnionType([
            new BuiltinType('null'),
            new BuiltinType('true'),
        ])), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_can_double_a_standalone_return_type_of_null(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone return type of null is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneReturnTypeNull'), []);
        $methodNode = $classNode->getMethods()['method'];

        $this->assertEquals(new ReturnTypeNode('null'), $methodNode->getReturnTypeNode());
    }

    #[Test]
    public function it_can_double_a_standalone_parameter_type_of_null(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Standalone parameter type of null is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\StandaloneParameterTypeNull'), []);
        $method = $classNode->getMethod('method');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode('null'), $arguments[0]->getTypeNode());
    }

    #[Test]
    public function it_reflects_read_only_class(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Read only classes are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(
            new \ReflectionClass('Fixtures\Prophecy\ReadOnlyClass'),
            []
        );

        $this->assertTrue($classNode->isReadOnly());
    }
}
