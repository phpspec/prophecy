<?php

namespace spec\Prophecy\Doubler\Generator;

use phpDocumentor\Reflection\DocBlock\Tags\Method;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class ClassCodeGeneratorSpec extends ObjectBehavior
{
    function it_generates_proper_php_code_for_specific_ClassNode(
        ClassNode $class,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3,
        ArgumentNode $argument11,
        ArgumentNode $argument12,
        ArgumentNode $argument21,
        ArgumentNode $argument31
    ) {
        $class->getParentClass()->willReturn('RuntimeException');
        $class->getInterfaces()->willReturn(array(
            'Prophecy\Doubler\Generator\MirroredInterface', 'ArrayAccess', 'ArrayIterator'
        ));
        $class->getProperties()->willReturn(array('name' => 'public', 'email' => 'private'));
        $class->getMethods()->willReturn(array($method1, $method2, $method3));

        $method1->getName()->willReturn('getName');
        $method1->getVisibility()->willReturn('public');
        $method1->returnsReference()->willReturn(false);
        $method1->isStatic()->willReturn(true);
        $method1->getArguments()->willReturn(array($argument11, $argument12));
        $method1->hasReturnType()->willReturn(true);
        $method1->getReturnType()->willReturn('string');
        $method1->getCode()->willReturn('return $this->name;');

        $method2->getName()->willReturn('getEmail');
        $method2->getVisibility()->willReturn('protected');
        $method2->returnsReference()->willReturn(false);
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument21));
        $method2->hasReturnType()->willReturn(false);
        $method2->getCode()->willReturn('return $this->email;');

        $method3->getName()->willReturn('getRefValue');
        $method3->getVisibility()->willReturn('public');
        $method3->returnsReference()->willReturn(true);
        $method3->isStatic()->willReturn(false);
        $method3->getArguments()->willReturn(array($argument31));
        $method3->hasReturnType()->willReturn(false);
        $method3->getCode()->willReturn('return $this->refValue;');

        $argument11->getName()->willReturn('fullname');
        $argument11->getTypeHint()->willReturn('array');
        $argument11->isOptional()->willReturn(true);
        $argument11->getDefault()->willReturn(null);
        $argument11->isPassedByReference()->willReturn(false);
        $argument11->isVariadic()->willReturn(false);

        $argument12->getName()->willReturn('class');
        $argument12->getTypeHint()->willReturn('ReflectionClass');
        $argument12->isOptional()->willReturn(false);
        $argument12->isPassedByReference()->willReturn(false);
        $argument12->isVariadic()->willReturn(false);

        $argument21->getName()->willReturn('default');
        $argument21->getTypeHint()->willReturn('string');
        $argument21->isOptional()->willReturn(true);
        $argument21->getDefault()->willReturn('ever.zet@gmail.com');
        $argument21->isPassedByReference()->willReturn(false);
        $argument21->isVariadic()->willReturn(false);

        $argument31->getName()->willReturn('refValue');
        $argument31->getTypeHint()->willReturn(null);
        $argument31->isOptional()->willReturn(false);
        $argument31->getDefault()->willReturn();
        $argument31->isPassedByReference()->willReturn(false);
        $argument31->isVariadic()->willReturn(false);

        $code = $this->generate('CustomClass', $class);

        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $expected = <<<'PHP'
namespace  {
class CustomClass extends \RuntimeException implements \Prophecy\Doubler\Generator\MirroredInterface, \ArrayAccess, \ArrayIterator {
public $name;
private $email;

public static function getName(array $fullname = NULL, \ReflectionClass $class): string {
return $this->name;
}
protected  function getEmail(string $default = 'ever.zet@gmail.com') {
return $this->email;
}
public  function &getRefValue( $refValue) {
return $this->refValue;
}

}
}
PHP;
        } else {
            $expected = <<<'PHP'
namespace  {
class CustomClass extends \RuntimeException implements \Prophecy\Doubler\Generator\MirroredInterface, \ArrayAccess, \ArrayIterator {
public $name;
private $email;

public static function getName(array $fullname = NULL, \ReflectionClass $class) {
return $this->name;
}
protected  function getEmail(\string $default = 'ever.zet@gmail.com') {
return $this->email;
}
public  function &getRefValue( $refValue) {
return $this->refValue;
}

}
}
PHP;
        }
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
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array(
            $method1, $method2, $method3, $method4
        ));

        $method1->getName()->willReturn('variadic');
        $method1->getVisibility()->willReturn('public');
        $method1->returnsReference()->willReturn(false);
        $method1->isStatic()->willReturn(false);
        $method1->getArguments()->willReturn(array($argument1));
        $method1->hasReturnType()->willReturn(false);
        $method1->getCode()->willReturn('');

        $method2->getName()->willReturn('variadicByRef');
        $method2->getVisibility()->willReturn('public');
        $method2->returnsReference()->willReturn(false);
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument2));
        $method2->hasReturnType()->willReturn(false);
        $method2->getCode()->willReturn('');

        $method3->getName()->willReturn('variadicWithType');
        $method3->getVisibility()->willReturn('public');
        $method3->returnsReference()->willReturn(false);
        $method3->isStatic()->willReturn(false);
        $method3->getArguments()->willReturn(array($argument3));
        $method3->hasReturnType()->willReturn(false);
        $method3->getCode()->willReturn('');

        $method4->getName()->willReturn('variadicWithTypeByRef');
        $method4->getVisibility()->willReturn('public');
        $method4->returnsReference()->willReturn(false);
        $method4->isStatic()->willReturn(false);
        $method4->getArguments()->willReturn(array($argument4));
        $method4->hasReturnType()->willReturn(false);
        $method4->getCode()->willReturn('');

        $argument1->getName()->willReturn('args');
        $argument1->getTypeHint()->willReturn(null);
        $argument1->isOptional()->willReturn(false);
        $argument1->isPassedByReference()->willReturn(false);
        $argument1->isVariadic()->willReturn(true);

        $argument2->getName()->willReturn('args');
        $argument2->getTypeHint()->willReturn(null);
        $argument2->isOptional()->willReturn(false);
        $argument2->isPassedByReference()->willReturn(true);
        $argument2->isVariadic()->willReturn(true);

        $argument3->getName()->willReturn('args');
        $argument3->getTypeHint()->willReturn('\ReflectionClass');
        $argument3->isOptional()->willReturn(false);
        $argument3->isPassedByReference()->willReturn(false);
        $argument3->isVariadic()->willReturn(true);

        $argument4->getName()->willReturn('args');
        $argument4->getTypeHint()->willReturn('\ReflectionClass');
        $argument4->isOptional()->willReturn(false);
        $argument4->isPassedByReference()->willReturn(true);
        $argument4->isVariadic()->willReturn(true);

        $code = $this->generate('CustomClass', $class);
        $expected = <<<'PHP'
namespace  {
class CustomClass extends \stdClass implements \Prophecy\Doubler\Generator\MirroredInterface {

public  function variadic( ...$args) {

}
public  function variadicByRef( &...$args) {

}
public  function variadicWithType(\\ReflectionClass ...$args) {

}
public  function variadicWithTypeByRef(\\ReflectionClass &...$args) {

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
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array($method));

        $method->getName()->willReturn('getName');
        $method->getVisibility()->willReturn('public');
        $method->isStatic()->willReturn(false);
        $method->getArguments()->willReturn(array($argument));
        $method->hasReturnType()->willReturn(false);
        $method->returnsReference()->willReturn(false);
        $method->getCode()->willReturn('return $this->name;');

        $argument->getName()->willReturn('fullname');
        $argument->getTypeHint()->willReturn('array');
        $argument->isOptional()->willReturn(true);
        $argument->getDefault()->willReturn(null);
        $argument->isPassedByReference()->willReturn(true);
        $argument->isVariadic()->willReturn(false);

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

    function it_generates_empty_class_for_empty_ClassNode(ClassNode $class)
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array());

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
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array());

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
}
