<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PHPSpec2\ObjectBehavior;

class DisableConstructorPatch extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function its_priority_is_100()
    {
        $this->getPriority()->shouldReturn(100);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_supports_nodes_with_constructor($node)
    {
        $node->hasMethod('__construct')->willReturn(true);

        $this->supports($node)->shouldReturn(true);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_does_not_support_nodes_with_constructor($node)
    {
        $node->hasMethod('__construct')->willReturn(false);

        $this->supports($node)->shouldReturn(false);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode    $class
     * @param Prophecy\Doubler\Generator\Node\MethodNode   $method
     * @param Prophecy\Doubler\Generator\Node\ArgumentNode $arg1
     * @param Prophecy\Doubler\Generator\Node\ArgumentNode $arg2
     */
    function it_makes_all_constructor_arguments_optional($class, $method, $arg1, $arg2)
    {
        $class->getMethod('__construct')->willReturn($method);
        $method->getArguments()->willReturn(array($arg1, $arg2));

        $arg1->setDefault(null)->shouldBeCalled();
        $arg2->setDefault(null)->shouldBeCalled();

        $this->apply($class);
    }
}
