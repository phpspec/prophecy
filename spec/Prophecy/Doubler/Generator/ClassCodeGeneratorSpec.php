<?php

namespace spec\Prophecy\Doubler\Generator;

use PhpSpec\ObjectBehavior;

class ClassCodeGeneratorSpec extends ObjectBehavior
{
    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode    $class
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method1
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method2
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument11
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument12
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument21
     */
    function it_generates_proper_php_code_for_specific_ClassNode(
        $class, $method1, $method2, $argument11, $argument12, $argument21
    )
    {
        $class->getParentClass()->willReturn('RuntimeException');
        $class->getInterfaces()->willReturn(array(
            'Prophecy\Doubler\Generator\MirroredInterface', 'ArrayAccess', 'ArrayIterator'
        ));
        $class->getProperties()->willReturn(array('name' => 'public', 'email' => 'private'));
        $class->getMethods()->willReturn(array($method1, $method2));

        $method1->getName()->willReturn('getName');
        $method1->getVisibility()->willReturn('public');
        $method1->isStatic()->willReturn(true);
        $method1->getArguments()->willReturn(array($argument11, $argument12));
        $method1->getCode()->willReturn('return $this->name;');

        $method2->getName()->willReturn('getEmail');
        $method2->getVisibility()->willReturn('protected');
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument21));
        $method2->getCode()->willReturn('return $this->email;');

        $argument11->getName()->willReturn('fullname');
        $argument11->getTypeHint()->willReturn('array');
        $argument11->hasDefault()->willReturn(true);
        $argument11->getDefault()->willReturn(null);
        $argument11->isPassedByReference()->willReturn(false);
        $argument11->isVariadic()->willReturn(false);

        $argument12->getName()->willReturn('class');
        $argument12->getTypeHint()->willReturn('ReflectionClass');
        $argument12->hasDefault()->willReturn(false);
        $argument12->isPassedByReference()->willReturn(false);
        $argument12->isVariadic()->willReturn(false);

        $argument21->getName()->willReturn('default');
        $argument21->getTypeHint()->willReturn(null);
        $argument21->hasDefault()->willReturn(true);
        $argument21->getDefault()->willReturn('ever.zet@gmail.com');
        $argument21->isPassedByReference()->willReturn(false);
        $argument21->isVariadic()->willReturn(false);

        $code = $this->generate('CustomClass', $class);
        $expected = <<<'PHP'
namespace  {
class CustomClass extends \RuntimeException implements \Prophecy\Doubler\Generator\MirroredInterface, \ArrayAccess, \ArrayIterator {
public $name;
private $email;

public static function getName(array $fullname = NULL, \ReflectionClass $class) {
return $this->name;
}
protected  function getEmail( $default = 'ever.zet@gmail.com') {
return $this->email;
}

}
}
PHP;
        $expected = strtr($expected, array("\r\n" => "\n", "\r" => "\n"));
        $code->shouldBe($expected);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode    $class
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method1
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method2
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method3
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method4
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument1
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument2
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument3
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument4
     */
    function it_generates_proper_php_code_for_variadics(
        $class, $method1, $method2, $method3, $method4, $argument1, $argument2,
        $argument3, $argument4
    )
    {
        $class->getParentClass()->willReturn('stdClass');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array(
            $method1, $method2, $method3, $method4
        ));

        $method1->getName()->willReturn('variadic');
        $method1->getVisibility()->willReturn('public');
        $method1->isStatic()->willReturn(false);
        $method1->getArguments()->willReturn(array($argument1));
        $method1->getCode()->willReturn('');

        $method2->getName()->willReturn('variadicByRef');
        $method2->getVisibility()->willReturn('public');
        $method2->isStatic()->willReturn(false);
        $method2->getArguments()->willReturn(array($argument2));
        $method2->getCode()->willReturn('');

        $method3->getName()->willReturn('variadicWithType');
        $method3->getVisibility()->willReturn('public');
        $method3->isStatic()->willReturn(false);
        $method3->getArguments()->willReturn(array($argument3));
        $method3->getCode()->willReturn('');

        $method4->getName()->willReturn('variadicWithTypeByRef');
        $method4->getVisibility()->willReturn('public');
        $method4->isStatic()->willReturn(false);
        $method4->getArguments()->willReturn(array($argument4));
        $method4->getCode()->willReturn('');

        $argument1->getName()->willReturn('args');
        $argument1->getTypeHint()->willReturn(null);
        $argument1->hasDefault()->willReturn(false);
        $argument1->isPassedByReference()->willReturn(false);
        $argument1->isVariadic()->willReturn(true);

        $argument2->getName()->willReturn('args');
        $argument2->getTypeHint()->willReturn(null);
        $argument2->hasDefault()->willReturn(false);
        $argument2->isPassedByReference()->willReturn(true);
        $argument2->isVariadic()->willReturn(true);

        $argument3->getName()->willReturn('args');
        $argument3->getTypeHint()->willReturn('\ReflectionClass');
        $argument3->hasDefault()->willReturn(false);
        $argument3->isPassedByReference()->willReturn(false);
        $argument3->isVariadic()->willReturn(true);

        $argument4->getName()->willReturn('args');
        $argument4->getTypeHint()->willReturn('\ReflectionClass');
        $argument4->hasDefault()->willReturn(false);
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

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode    $class
     * @param \Prophecy\Doubler\Generator\Node\MethodNode   $method
     * @param \Prophecy\Doubler\Generator\Node\ArgumentNode $argument
     */
    function it_overrides_properly_methods_with_args_passed_by_reference(
        $class, $method, $argument
    )
    {
        $class->getParentClass()->willReturn('RuntimeException');
        $class->getInterfaces()->willReturn(array('Prophecy\Doubler\Generator\MirroredInterface'));
        $class->getProperties()->willReturn(array());
        $class->getMethods()->willReturn(array($method));

        $method->getName()->willReturn('getName');
        $method->getVisibility()->willReturn('public');
        $method->isStatic()->willReturn(false);
        $method->getArguments()->willReturn(array($argument));
        $method->getCode()->willReturn('return $this->name;');

        $argument->getName()->willReturn('fullname');
        $argument->getTypeHint()->willReturn('array');
        $argument->hasDefault()->willReturn(true);
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

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $class
     */
    function it_generates_empty_class_for_empty_ClassNode($class)
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

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $class
     */
    function it_wraps_class_in_namespace_if_it_is_namespaced($class)
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
