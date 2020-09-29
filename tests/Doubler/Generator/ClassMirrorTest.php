<?php

namespace Tests\Prophecy\Doubler\Generator;

use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Exception\Doubler\ClassMirrorException;
use Prophecy\Exception\InvalidArgumentException;

class ClassMirrorTest extends TestCase
{
    /**
     * @test
     */
    public function it_reflects_allowed_magic_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\SpecialMethods');

        $mirror = new ClassMirror();

        $node = $mirror->reflect($class, array());

        $this->assertCount(7, $node->getMethods());
    }

    /**
     * @test
     */
    public function it_reflects_protected_abstract_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithProtectedAbstractMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertEquals('Fixtures\Prophecy\WithProtectedAbstractMethod', $classNode->getParentClass());

        $methodNodes = $classNode->getMethods();
        $this->assertCount(1, $methodNodes);

        $this->assertEquals('protected', $methodNodes['innerDetail']->getVisibility());
    }

    /**
     * @test
     */
    public function it_reflects_public_static_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithStaticMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertEquals('Fixtures\Prophecy\WithStaticMethod', $classNode->getParentClass());

        $methodNodes = $classNode->getMethods();
        $this->assertCount(1, $methodNodes);

        $this->assertTrue($methodNodes['innerDetail']->isStatic());
    }

    /**
     * @test
     */
    public function it_marks_required_args_without_types_as_not_optional()
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

    /**
     * @test
     */
    public function it_properly_reads_methods_arguments_with_types()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithArguments');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(3, $argNodes);


        $this->assertEquals('arg_1', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode('ArrayAccess'), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());

        $this->assertEquals('arg_2', $argNodes[1]->getName());
        $this->assertEquals(new ArgumentTypeNode('array'), $argNodes[1]->getTypeNode());
        $this->assertTrue($argNodes[1]->isOptional());
        $this->assertEquals(array(), $argNodes[1]->getDefault());
        $this->assertFalse($argNodes[1]->isPassedByReference());
        $this->assertFalse($argNodes[1]->isVariadic());

        $this->assertEquals('arg_3', $argNodes[2]->getName());
        $this->assertEquals(new ArgumentTypeNode('ArrayAccess', 'null'), $argNodes[2]->getTypeNode());
        $this->assertTrue($argNodes[2]->isOptional());
        $this->assertNull($argNodes[2]->getDefault());
        $this->assertFalse($argNodes[2]->isPassedByReference());
        $this->assertFalse($argNodes[2]->isVariadic());
    }

    /**
     * @test
     * @requires PHP 5.4
     */
    public function it_properly_reads_methods_arguments_with_callable_types()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithCallableArgument');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(2, $argNodes);

        $this->assertEquals('arg_1', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode('callable'), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertFalse($argNodes[0]->isVariadic());

        $this->assertEquals('arg_2', $argNodes[1]->getName());
        $this->assertEquals(new ArgumentTypeNode('callable', 'null'), $argNodes[1]->getTypeNode());
        $this->assertTrue($argNodes[1]->isOptional());
        $this->assertNull($argNodes[1]->getDefault());
        $this->assertFalse($argNodes[1]->isPassedByReference());
        $this->assertFalse($argNodes[1]->isVariadic());
    }

    /**
     * @test
     * @requires PHP 5.6
     */
    public function it_properly_reads_methods_variadic_arguments()
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

    /**
     * @test
     * @requires PHP 5.6
     */
    public function it_properly_reads_methods_typehinted_variadic_arguments()
    {
        if (defined('HHVM_VERSION_ID')) {
            $this->markTestSkipped('HHVM does not support typehints on variadic arguments.');
        }

        $class = new \ReflectionClass('Fixtures\Prophecy\WithTypehintedVariadicArgument');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());
        $methodNode = $classNode->getMethod('methodWithTypeHintedArgs');
        $argNodes = $methodNode->getArguments();

        $this->assertCount(1, $argNodes);

        $this->assertEquals('args', $argNodes[0]->getName());
        $this->assertEquals(new ArgumentTypeNode('array'), $argNodes[0]->getTypeNode());
        $this->assertFalse($argNodes[0]->isOptional());
        $this->assertFalse($argNodes[0]->isPassedByReference());
        $this->assertTrue($argNodes[0]->isVariadic());
    }

    /**
     * @test
     */
    public function it_marks_passed_by_reference_args_as_passed_by_reference()
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

    /**
     * @test
     */
    public function it_throws_an_exception_if_class_is_final()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\FinalClass');

        $mirror = new ClassMirror();

        $this->expectException(ClassMirrorException::class);

        $mirror->reflect($class, array());
    }

    /**
     * @test
     */
    public function it_ignores_final_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(0, $classNode->getMethods());
    }

    /**
     * @test
     */
    public function it_marks_final_methods_as_unextendable()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(1, $classNode->getUnextendableMethods());
        $this->assertFalse($classNode->isExtendable('finalImplementation'));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_interface_provided_instead_of_class()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\EmptyInterface');

        $mirror = new ClassMirror();

        $this->expectException(InvalidArgumentException::class);

        $mirror->reflect($class, array());
    }

    /**
     * @test
     */
    public function it_reflects_all_interfaces_methods()
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

    /**
     * @test
     */
    public function it_ignores_virtually_private_methods()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithVirtuallyPrivateMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(2, $classNode->getMethods());
        $this->assertTrue($classNode->hasMethod('isAbstract'));
        $this->assertTrue($classNode->hasMethod('__toString'));
        $this->assertFalse($classNode->hasMethod('_getName'));
    }

    /**
     * @test
     */
    public function it_does_not_throw_exception_for_virtually_private_finals()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithFinalVirtuallyPrivateMethod');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(0, $classNode->getMethods());
    }

    /**
     * @test
     * @requires PHP 7
     */
    public function it_reflects_return_typehints()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\WithReturnTypehints');

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class, array());

        $this->assertCount(3, $classNode->getMethods());
        $this->assertTrue($classNode->hasMethod('getName'));
        $this->assertTrue($classNode->hasMethod('getSelf'));
        $this->assertTrue($classNode->hasMethod('getParent'));

        $this->assertEquals(new ReturnTypeNode('string'), $classNode->getMethod('getName')->getReturnTypeNode());
        $this->assertEquals(new ReturnTypeNode('\Fixtures\Prophecy\WithReturnTypehints'), $classNode->getMethod('getSelf')->getReturnTypeNode());
        $this->assertEquals(new ReturnTypeNode('\Fixtures\Prophecy\EmptyClass'), $classNode->getMethod('getParent')->getReturnTypeNode());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_class_provided_in_interfaces_list()
    {
        $class = new \ReflectionClass('Fixtures\Prophecy\EmptyClass');

        $mirror = new ClassMirror();

        $this->expectException(\InvalidArgumentException::class);

        $mirror->reflect(null, array($class));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_not_reflection_provided_as_interface()
    {
        $mirror = new ClassMirror();

        $this->expectException(\InvalidArgumentException::class);

        $mirror->reflect(null, array(null));
    }

    /**
     * @test
     */
    public function it_doesnt_use_scalar_typehints()
    {
        if (PHP_VERSION_ID >= 80000) {
            $this->markTestSkipped('Internal types have scalar hints in PHP 8');
        }

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('ReflectionMethod'), array());
        $method = $classNode->getMethod('export');
        $arguments = $method->getArguments();

        $this->assertEquals(new ArgumentTypeNode(), $arguments[0]->getTypeNode());
        $this->assertEquals(new ArgumentTypeNode(), $arguments[1]->getTypeNode());
        $this->assertEquals(new ArgumentTypeNode(), $arguments[2]->getTypeNode());
    }

    /**
     * @test
     */
    public function it_doesnt_fail_to_typehint_nonexistent_FQCN()
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\OptionalDepsClass'), array());
        $method = $classNode->getMethod('iHaveAStrangeTypeHintedArg');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode('I\Simply\Am\Nonexistent'), $arguments[0]->getTypeNode());
    }

    /**
     * @test
     * @requires PHP 7.1
     */
    public function it_doesnt_fail_on_array_nullable_parameter_with_not_null_default_value()
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\NullableArrayParameter'), array());
        $method = $classNode->getMethod('iHaveNullableArrayParameterWithNotNullDefaultValue');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode('array', 'null'), $arguments[0]->getTypeNode());
    }

    /**
     * @test
     */
    public function it_doesnt_fail_to_typehint_nonexistent_RQCN()
    {
        $mirror = new ClassMirror();

        $classNode = $mirror->reflect(new \ReflectionClass('Fixtures\Prophecy\OptionalDepsClass'), array());
        $method = $classNode->getMethod('iHaveAnEvenStrangerTypeHintedArg');
        $arguments = $method->getArguments();
        $this->assertEquals(new ArgumentTypeNode('I\Simply\Am\Not'), $arguments[0]->getTypeNode());
    }

    /**
     * @test
     * @requires PHP 7.2
     */
    function it_doesnt_fail_when_method_is_extended_with_more_params()
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

    /**
     * @test
     * @doesNotPerformAssertions
     */
    function it_doesnt_fail_to_mock_self_referencing_interface()
    {
        $class = $this->prophesize('Fixtures\Prophecy\SelfReferencing');
        $class->reveal();
    }

    /**
     * @test
     */
    function it_changes_argument_names_if_they_are_varying()
    {
        // Use test doubles in this test, as arguments named ... in the Reflection API can only happen for internal classes
        $class = $this->prophesize('ReflectionClass');
        $method = $this->prophesize('ReflectionMethod');
        $parameter = $this->prophesize('ReflectionParameter');

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

        $parameter->getName()->willReturn('...');
        $parameter->isDefaultValueAvailable()->willReturn(true);
        $parameter->getDefaultValue()->willReturn(null);
        $parameter->isPassedByReference()->willReturn(false);
        $parameter->allowsNull()->willReturn(true);
        $parameter->getClass()->willReturn($class);
        $parameter->getType()->willReturn(null);
        $parameter->hasType()->willReturn(false);
        $parameter->isVariadic()->willReturn(false);
        $parameter->getDeclaringClass()->willReturn($class);

        $mirror = new ClassMirror();

        $classNode = $mirror->reflect($class->reveal(), array());

        $methodNodes = $classNode->getMethods();

        $argumentNodes = $methodNodes['methodName']->getArguments();
        $argumentNode = $argumentNodes[0];

        $this->assertEquals('__dot_dot_dot__', $argumentNode->getName());
    }

    /** @test */
    public function it_can_double_a_class_with_union_return_types()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionReturnTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertSame(['\stdClass', 'bool'], $methodNode->getReturnTypeNode()->getTypes());
    }

    /** @test */
    public function it_can_double_a_class_with_union_argument_types()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Union types are not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\UnionArgumentTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertEquals(new ArgumentTypeNode('bool', '\\stdClass'), $methodNode->getArguments()[0]->getTypeNode());
    }

    /** @test */
    public function it_can_double_a_class_with_mixed_types()
    {
        if (PHP_VERSION_ID < 80000) {
            $this->markTestSkipped('Mixed type is not supported in this PHP version');
        }

        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\MixedTypes'), []);
        $methodNode = $classNode->getMethods()['doSomething'];

        $this->assertEquals(new ArgumentTypeNode('mixed'), $methodNode->getArguments()[0]->getTypeNode());
        $this->assertEquals(new ReturnTypeNode('mixed'), $methodNode->getReturnTypeNode());
    }

    /** @test */
    public function it_can_double_inherited_self_return_type()
    {
        $classNode = (new ClassMirror())->reflect(new \ReflectionClass('Fixtures\Prophecy\ClassExtendAbstractWithMethodWithReturnType'), []);
        $methodNode = $classNode->getMethods()['returnSelf'];

        $this->assertEquals(new ReturnTypeNode('Fixtures\Prophecy\AbstractBaseClassWithMethodWithReturnType'), $methodNode->getReturnTypeNode());
    }

    /**
     * @test
     */
    public function case_insensitive_method_names()
    {
        $prophecy = $this->prophesize('ArrayObject');
        $prophecy->offsetGet(1)->willReturn(1)->shouldBeCalledTimes(1);
        $prophecy->offsetget(2)->willReturn(2)->shouldBeCalledTimes(1);
        $prophecy->OffsetGet(3)->willReturn(3)->shouldBeCalledTimes(1);

        $arrayObject = $prophecy->reveal();
        self::assertSame(1, $arrayObject->offsetGet(1));
        self::assertSame(2, $arrayObject->offsetGet(2));
        self::assertSame(3, $arrayObject->offsetGet(3));
    }
}
