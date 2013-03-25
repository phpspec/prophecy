<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PHPSpec2\ObjectBehavior;

class ProphecySubjectPatch extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function it_has_priority_of_0()
    {
        $this->getPriority()->shouldReturn(0);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_supports_any_class($node)
    {
        $this->supports($node)->shouldReturn(true);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_forces_class_to_implement_ProphecySubjectInterface($node)
    {
        $node->addInterface('Prophecy\Prophecy\ProphecySubjectInterface')->shouldBeCalled();
        $this->apply($node);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode  $node
     * @param Prophecy\Doubler\Generator\Node\MethodNode $constructor
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method1
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method2
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method3
     */
    function it_forces_all_class_methods_except_constructor_to_proxy_calls_into_prophecy_makeCall(
        $node, $constructor, $method1, $method2, $method3
    )
    {
        $constructor->getName()->willReturn('__construct');
        $method1->getName()->willReturn('method1');
        $method2->getName()->willReturn('method2');
        $method3->getName()->willReturn('method3');

        $node->getMethods()->willReturn(array(
            'method1' => $method1,
            'method2' => $method2,
            'method3' => $method3,
        ));

        $constructor->setCode(ANY_ARGUMENT)->shouldNotBeCalled();

        $method1->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method2->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method3->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();

        $this->apply($node);
    }
}
