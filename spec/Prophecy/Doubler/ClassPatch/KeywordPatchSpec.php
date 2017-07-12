<?php

namespace spec\Prophecy\Doubler\ClassPatch;

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

    function it_will_not_remove_echo_and_eval_methods_for_php7_and_above(
        ClassNode $node,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3
    ) {
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $node->removeMethod('eval')->shouldNotBeCalled();
            $node->removeMethod('echo')->shouldNotBeCalled();

            $method1->getName()->willReturn('echo');
            $method2->getName()->willReturn('eval');
            $method3->getName()->willReturn('notKeyword');

            $node->getMethods()->willReturn(array(
                'echo' => $method1,
                'eval' => $method2,
                'notKeyword' => $method3,
            ));

            $this->apply($node);
        }
    }

    function it_will_remove_echo_and_eval_methods_for_php5_and_inferior(
        ClassNode $node,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3
    ) {
        if (version_compare(PHP_VERSION, '7', '<')) {
            $node->removeMethod('eval')->shouldBeCalled();
            $node->removeMethod('echo')->shouldBeCalled();

            $method1->getName()->willReturn('echo');
            $method2->getName()->willReturn('eval');
            $method3->getName()->willReturn('notKeyword');

            $node->getMethods()->willReturn(array(
                'echo' => $method1,
                'eval' => $method2,
                'notKeyword' => $method3,
            ));

            $this->apply($node);
        }
    }
}
