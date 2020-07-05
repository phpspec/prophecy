<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class KeywordPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function its_priority_is_49()
    {
        $this->getPriority()->shouldReturn(49);
    }

    function it_will_remove_halt_compiler_method(
        ClassNode $node,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3
    ) {
        $node->removeMethod('__halt_compiler')->shouldBeCalled();

        $method1->getName()->willReturn('__halt_compiler');
        $method2->getName()->willReturn('echo');
        $method3->getName()->willReturn('notKeyword');

        $node->getMethods()->willReturn(array(
            '__halt_compiler' => $method1,
            'echo' => $method2,
            'notKeyword' => $method3,
        ));

        $this->apply($node);
    }
}
