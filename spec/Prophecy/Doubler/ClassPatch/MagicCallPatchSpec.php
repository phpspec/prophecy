<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class MagicCallPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_supports_anything($node)
    {
        $this->supports($node)->shouldReturn(true);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_discovers_api_using_phpdoc($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApi');

        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_ignores_existing_methods($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiExtended');

        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();
        $node->addMethod(new MethodNode('definedMethod'))->shouldNotBeCalled();

        $this->apply($node);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_discovers_api_with_parameters_using_phpdoc($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalParametrizedApi');

        $method = new MethodNode('simpleParam');
        $method->addArgument(new ArgumentNode('param1'));
        $method->addArgument(new ArgumentNode('second'));

        $node->addMethod(Argument::any())->willReturn(null);
        $node->addMethod($method)->shouldBeCalled();

        $this->apply($node);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_discovers_api_with_hinted_parameters_using_phpdoc($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalParametrizedApi');

        $method = new MethodNode('withHintedType');

        $argument = new ArgumentNode('floatVar');
        $argument->setDefault("2.3");
        $argument->setTypeHint("float");
        $method->addArgument($argument);

        $argument = new ArgumentNode('class');
        $argument->setTypeHint('\spec\Prophecy\Doubler\ClassPatch\MagicalApi');
        $method->addArgument($argument);

        $node->addMethod(Argument::any())->willReturn(null);
        $node->addMethod($method)->shouldBeCalled();

        $this->apply($node);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_discovers_api_with_parameter_default_values_using_phpdoc($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalParametrizedApi');

        $method = new MethodNode('withDefaultValue');

        $argument = new ArgumentNode('singleQuote');
        $argument->setDefault("value");
        $method->addArgument($argument);

        $argument = new ArgumentNode('doubleQuote');
        $argument->setDefault('value');
        $method->addArgument($argument);

        $argument = new ArgumentNode('const');
        $argument->setDefault('\DateTime::RSS');
        $method->addArgument($argument);

        $node->addMethod(Argument::any())->willReturn(null);
        $node->addMethod($method)->shouldBeCalled();

        $this->apply($node);
    }

    function it_has_50_priority()
    {
        $this->getPriority()->shouldReturn(50);
    }
}

/**
 * @method void undefinedMethod()
 */
class MagicalApi
{
    /**
     * @return void
     */
    public function definedMethod()
    {

    }
}

/**
 * @method void simpleParam($param1,  $second )
 * @method void withDefaultValue($singleQuote = 'value', $doubleQuote="value", $const= \DateTime::RSS)
 * @method void withHintedType(float $floatVar = 2.3, \spec\Prophecy\Doubler\ClassPatch\MagicalApi $class)
 */
class MagicalParametrizedApi
{

}

/**
 * @method void undefinedMethod()
 * @method void definedMethod()
 */
class MagicalApiExtended extends MagicalApi
{

}
