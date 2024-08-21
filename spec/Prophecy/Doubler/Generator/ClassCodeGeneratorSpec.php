<?php

namespace spec\Prophecy\Doubler\Generator;

use phpDocumentor\Reflection\DocBlock\Tags\Method;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\Doubler\Generator\Node\PropertyNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;

class ClassCodeGeneratorSpec extends ObjectBehavior
{
    function it_generates_proper_php_code_for_specific_ClassNode(
        ClassNode $class,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3,
        MethodNode $method4,
        MethodNode $method5,
        ArgumentNode $argument11,
        ArgumentNode $argument12,
        ArgumentNode $argument13,
        ArgumentNode $argument21,
        ArgumentNode $argument31
    ) {
        $class->getParentClass()->willReturn('RuntimeException');
        $class->getInterfaces()->willReturn(array(
            'Prophecy\Doubler\Generator\MirroredInterface', 'ArrayAccess', 'ArrayIterator'
        ));
        $name = new PropertyNode('name');
        $name->setVisibility('public');
        $email = new PropertyNode('email');
        $email->setVisibility('private');
        $class->getPropertyNodes()->willReturn(array('name' => $name, 'email' => $email));
        $class->getMethods()->willReturn(array($method1, $method2, $method3, $method4, $method5));
        $class->isReadOnly()->willReturn(false);

        $method1->getName()->willReturn('getName');
        $method1->getVisibility()->willReturn('public');
        $method1->returnsReference()->willReturn(false);
        $method1->isStatic()->willReturn(true);
        $method1->getArguments()->willReturn(array($argument11, $argument12, $argument13));
        $method1->getReturnTypeNode()->willReturn(new ReturnTypeNode('string', 'null'));
        $method1->getCode()->willReturn('return $this->name;');

        $method2->getName()->willReturn('getEmail');
        $method2->getVisibility()->willReturn('protected');
        $method2->returnsReference()->willReturn(false);
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument21));
        $method2->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method2->getCode()->willReturn('return $this->email;');

        $method3->getName()->willReturn('getRefValue');
        $method3->getVisibility()->willReturn('public');
        $method3->returnsReference()->willReturn(true);
        $method3->isStatic()->willReturn(false);
        $method3->getArguments()->willReturn(array($argument31));
        $method3->getReturnTypeNode()->willReturn(new ReturnTypeNode('string'));
        $method3->getCode()->willReturn('return $this->refValue;');

        $method4->getName()->willReturn('doSomething');
        $method4->getVisibility()->willReturn('public');
        $method4->returnsReference()->willReturn(false);
        $method4->isStatic()->willReturn(false);
        $method4->getArguments()->willReturn(array());
        $method4->getReturnTypeNode()->willReturn(new ReturnTypeNode('void'));
        $method4->getCode()->willReturn('return;');

        $method5->getName()->willReturn('returnObject');
        $method5->getVisibility()->willReturn('public');
        $method5->returnsReference()->willReturn(false);
        $method5->isStatic()->willReturn(false);
        $method5->getArguments()->willReturn(array());
        $method5->getReturnTypeNode()->willReturn(new ReturnTypeNode('object'));
        $method5->getCode()->willReturn('return;');

        $argument11->getName()->willReturn('fullname');
        $argument11->isOptional()->willReturn(true);
        $argument11->getDefault()->willReturn(null);
        $argument11->isPassedByReference()->willReturn(false);
        $argument11->isVariadic()->willReturn(false);
        $argument11->getTypeNode()->willReturn(new ArgumentTypeNode('array'));

        $argument12->getName()->willReturn('class');
        $argument12->isOptional()->willReturn(false);
        $argument12->isPassedByReference()->willReturn(false);
        $argument12->isVariadic()->willReturn(false);
        $argument12->getTypeNode()->willReturn(new ArgumentTypeNode('ReflectionClass'));

        $argument13->getName()->willReturn('instance');
        $argument13->isOptional()->willReturn(false);
        $argument13->isPassedByReference()->willReturn(false);
        $argument13->isVariadic()->willReturn(false);
        $argument13->getTypeNode()->willReturn(new ArgumentTypeNode('object'));

        $argument21->getName()->willReturn('default');
        $argument21->isOptional()->willReturn(true);
        $argument21->getDefault()->willReturn('ever.zet@gmail.com');
        $argument21->isPassedByReference()->willReturn(false);
        $argument21->isVariadic()->willReturn(false);
        $argument21->getTypeNode()->willReturn(new ArgumentTypeNode('string', 'null'));

        $argument31->getName()->willReturn('refValue');
        $argument31->isOptional()->willReturn(false);
        $argument31->getDefault()->willReturn();
        $argument31->isPassedByReference()->willReturn(false);
        $argument31->isVariadic()->willReturn(false);
        $argument31->getTypeNode()->willReturn(new ArgumentTypeNode());


        $code = $this->generate('CustomClass', $class);

        $expected = <<<'PHP'
namespace  {
class CustomClass extends \RuntimeException implements \Prophecy\Doubler\Generator\MirroredInterface, \ArrayAccess, \ArrayIterator {
public $name;
private $email;

public static function getName(array $fullname = NULL, \ReflectionClass $class, object $instance): ?string {
return $this->name;
}
protected  function getEmail(?string $default = 'ever.zet@gmail.com') {
return $this->email;
}
public  function &getRefValue( $refValue): string {
return $this->refValue;
}
public  function doSomething(): void {
return;
}
public  function returnObject(): object {
return;
}

}
}
PHP;

        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    function it_generates_proper_php_code_for_variadics(
        ClassNode $class,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3,
        MethodNode $method4,
        ArgumentNode $argument1,
        ArgumentNode $argument2,
        ArgumentNode $argument3,
        ArgumentNode $argument4
    ) {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getPropertyNodes()->willReturn(array());
        $class->getMethods()->willReturn(array(
            $method1, $method2, $method3, $method4
        ));
        $class->isReadOnly()->willReturn(false);

        $method1->getName()->willReturn('variadic');
        $method1->getVisibility()->willReturn('public');
        $method1->returnsReference()->willReturn(false);
        $method1->isStatic()->willReturn(false);
        $method1->getArguments()->willReturn(array($argument1));
        $method1->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method1->getCode()->willReturn('');

        $method2->getName()->willReturn('variadicByRef');
        $method2->getVisibility()->willReturn('public');
        $method2->returnsReference()->willReturn(false);
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument2));
        $method2->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method2->getCode()->willReturn('');

        $method3->getName()->willReturn('variadicWithType');
        $method3->getVisibility()->willReturn('public');
        $method3->returnsReference()->willReturn(false);
        $method3->isStatic()->willReturn(false);
        $method3->getArguments()->willReturn(array($argument3));
        $method3->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method3->getCode()->willReturn('');

        $method4->getName()->willReturn('variadicWithTypeByRef');
        $method4->getVisibility()->willReturn('public');
        $method4->returnsReference()->willReturn(false);
        $method4->isStatic()->willReturn(false);
        $method4->getArguments()->willReturn(array($argument4));
        $method4->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method4->getCode()->willReturn('');

        $argument1->getName()->willReturn('args');
        $argument1->isOptional()->willReturn(false);
        $argument1->isPassedByReference()->willReturn(false);
        $argument1->isVariadic()->willReturn(true);
        $argument1->getTypeNode()->willReturn(new ArgumentTypeNode());

        $argument2->getName()->willReturn('args');
        $argument2->isOptional()->willReturn(false);
        $argument2->isPassedByReference()->willReturn(true);
        $argument2->isVariadic()->willReturn(true);
        $argument2->getTypeNode()->willReturn(new ArgumentTypeNode());

        $argument3->getName()->willReturn('args');
        $argument3->isOptional()->willReturn(false);
        $argument3->isPassedByReference()->willReturn(false);
        $argument3->isVariadic()->willReturn(true);
        $argument3->getTypeNode()->willReturn(new ArgumentTypeNode('ReflectionClass'));

        $argument4->getName()->willReturn('args');
        $argument4->isOptional()->willReturn(false);
        $argument4->isPassedByReference()->willReturn(true);
        $argument4->isVariadic()->willReturn(true);
        $argument4->getTypeNode()->willReturn(new ArgumentTypeNode('ReflectionClass'));


        $code = $this->generate('CustomClass', $class);
        $expected = <<<'PHP'
namespace  {
class CustomClass extends \stdClass implements \Prophecy\Doubler\Generator\MirroredInterface {

public  function variadic( ...$args) {

}
public  function variadicByRef( &...$args) {

}
public  function variadicWithType(\ReflectionClass ...$args) {

}
public  function variadicWithTypeByRef(\ReflectionClass &...$args) {

}

}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    function it_overrides_properly_methods_with_args_passed_by_reference(
        ClassNode $class,
        MethodNode $method,
        ArgumentNode $argument
    ) {
        $class->getParentClass()->willReturn('RuntimeException');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getPropertyNodes()->willReturn(array());
        $class->getMethods()->willReturn(array($method));
        $class->isReadOnly()->willReturn(false);

        $method->getName()->willReturn('getName');
        $method->getVisibility()->willReturn('public');
        $method->isStatic()->willReturn(false);
        $method->getArguments()->willReturn(array($argument));
        $method->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method->returnsReference()->willReturn(false);
        $method->getCode()->willReturn('return $this->name;');

        $argument->getName()->willReturn('fullname');
        $argument->isOptional()->willReturn(true);
        $argument->getDefault()->willReturn(null);
        $argument->isPassedByReference()->willReturn(true);
        $argument->isVariadic()->willReturn(false);
        $argument->getTypeNode()->willReturn(new ArgumentTypeNode('array'));

        $code = $this->generate('CustomClass', $class);
        $expected =<<<'PHP'
namespace  {
class CustomClass extends \RuntimeException implements \Prophecy\Doubler\Generator\MirroredInterface {

public  function getName(array &$fullname = NULL) {
return $this->name;
}

}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    function it_generates_proper_code_for_union_return_types
    (
        ClassNode $class,
        MethodNode $method
    )
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn([]);
        $class->getPropertyNodes()->willReturn([]);
        $class->getMethods()->willReturn(array($method));
        $class->isReadOnly()->willReturn(false);

        $method->getName()->willReturn('foo');
        $method->getVisibility()->willReturn('public');
        $method->isStatic()->willReturn(false);
        $method->getArguments()->willReturn([]);
        $method->getReturnTypeNode()->willReturn(new ReturnTypeNode('int', 'string', 'null'));
        $method->returnsReference()->willReturn(false);
        $method->getCode()->willReturn('');

        $code = $this->generate('CustomClass', $class);

        $expected =<<<'PHP'
namespace  {
class CustomClass extends \stdClass implements  {

public  function foo(): int|string|null {

}

}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));

        $code->shouldBe($expected);
    }

    function it_generates_proper_code_for_union_argument_types
    (
        ClassNode $class,
        MethodNode $method,
        ArgumentNode $argument
    )
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn([]);
        $class->getPropertyNodes()->willReturn([]);
        $class->getMethods()->willReturn(array($method));
        $class->isReadOnly()->willReturn(false);

        $method->getName()->willReturn('foo');
        $method->getVisibility()->willReturn('public');
        $method->isStatic()->willReturn(false);
        $method->getArguments()->willReturn([$argument]);
        $method->getReturnTypeNode()->willReturn(new ReturnTypeNode());
        $method->returnsReference()->willReturn(false);
        $method->getCode()->willReturn('');

        $argument->getTypeNode()->willReturn(new ArgumentTypeNode('int', 'string', 'null'));
        $argument->getName()->willReturn('arg');
        $argument->isPassedByReference()->willReturn(false);
        $argument->isVariadic()->willReturn(false);
        $argument->isOptional()->willReturn(false);

        $code = $this->generate('CustomClass', $class);

        $expected =<<<'PHP'
namespace  {
class CustomClass extends \stdClass implements  {

public  function foo(int|string|null $arg) {

}

}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));

        $code->shouldBe($expected);
    }

    function it_generates_empty_class_for_empty_ClassNode(ClassNode $class)
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getPropertyNodes()->willReturn(array());
        $class->getMethods()->willReturn(array());
        $class->isReadOnly()->willReturn(false);

        $code = $this->generate('CustomClass', $class);
        $expected =<<<'PHP'
namespace  {
class CustomClass extends \stdClass implements \Prophecy\Doubler\Generator\MirroredInterface {


}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    function it_wraps_class_in_namespace_if_it_is_namespaced(ClassNode $class)
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getPropertyNodes()->willReturn(array());
        $class->getMethods()->willReturn(array());
        $class->isReadOnly()->willReturn(false);

        $code = $this->generate('My\Awesome\CustomClass', $class);
        $expected =<<<'PHP'
namespace My\Awesome {
class CustomClass extends \stdClass implements \Prophecy\Doubler\Generator\MirroredInterface {


}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    function it_generates_read_only_class_if_parent_class_is_read_only(ClassNode $class)
    {
        $class->getParentClass()->willReturn('ReadOnlyClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getPropertyNodes()->willReturn(array());
        $class->getMethods()->willReturn(array());
        $class->isReadOnly()->willReturn(true);

        $code = $this->generate('CustomClass', $class);
        $expected =<<<'PHP'
namespace  {
readonly class CustomClass extends \ReadOnlyClass implements \Prophecy\Doubler\Generator\MirroredInterface {


}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }
}
