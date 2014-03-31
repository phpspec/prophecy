<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\MethodNode;

class RedisEchoEvalPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function its_priority_is_50()
    {
        $this->getPriority()->shouldReturn(50);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_will_remove_echo_and_eval_methods($node)
    {
        $node->removeMethod('eval')->shouldBeCalled();
        $node->removeMethod('echo')->shouldBeCalled();

        $node->addMethod(new MethodNode('echo'));
        $node->addMethod(new MethodNode('eval'));
        $node->addMethod(new MethodNode('get'));

        $this->apply($node);
    }
}