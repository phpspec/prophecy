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
    function it_discovers_api_with_parameters_using_phpdoc($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalParametrizedApi');

        $method = new MethodNode('parametrizedMethod');
        $method->addArgument(new ArgumentNode('param'));
        $argumentWithDefaultValue = new ArgumentNode('param');
        $argumentWithDefaultValue->setDefault('value');
        $method->addArgument($argumentWithDefaultValue);
        $method->addArgument($argumentWithDefaultValue);
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
 * @method void definedMethod()
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
 * @method void parametrizedMethod($param, $param = 'value', $param = "value")
 */
class MagicalParametrizedApi
{
    /**
     * @return void
     */
    public function definedMethod()
    {

    }
}