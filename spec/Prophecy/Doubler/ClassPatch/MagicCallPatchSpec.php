<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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
        $node->hasMethod('undefinedMethod')->willReturn(false);
        $node->hasMethod('undefinedInterfaceMethod')->willReturn(false);
        $node->getInterfaces()->willReturn(['spec\Prophecy\Doubler\ClassPatch\MagicalInterface']);

        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();
        $node->addMethod(new MethodNode('undefinedInterfaceMethod'))->shouldBeCalled();

        $this->apply($node);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_ignores_existing_methods($node)
    {
        $node->getParentClass()->willReturn('spec\Prophecy\Doubler\ClassPatch\MagicalApiExtended');
        $node->getInterfaces()->shouldBeCalled();

        $node->hasMethod('undefinedMethod')->willReturn(false);
        $node->addMethod(new MethodNode('undefinedMethod'))->shouldBeCalled();

        $node->hasMethod('definedMethod')->willReturn(true);
        $node->addMethod(new MethodNode('definedMethod'))->shouldNotBeCalled();

        $this->apply($node);
    }

    function it_has_50_priority()
    {
        $this->getPriority()->shouldReturn(50);
    }
}

/**
 * @method void undefinedInterfaceMethod()
 */
interface MagicalInterface {

}

/**
 * @method void undefinedMethod()
 */
class MagicalApi implements MagicalInterface
{
    /**
     * @return void
     */
    public function definedMethod()
    {

    }
}

/**
 * @method void undefinedMethod()
 * @method void definedMethod()
 */
class MagicalApiExtended extends MagicalApi
{

}