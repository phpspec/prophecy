<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class ProphecySubjectPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function it_has_priority_of_0()
    {
        $this->getPriority()->shouldReturn(0);
    }

    function it_supports_any_class(ClassNode $node)
    {
        $this->supports($node)->shouldReturn(true);
    }

    function it_forces_class_to_implement_ProphecySubjectInterface(ClassNode $node)
    {
        $node->addInterface('Prophecy\Prophecy\ProphecySubjectInterface')->shouldBeCalled();

        $node->addProperty('objectProphecy', 'private')->willReturn(null);
        $node->getMethods()->willReturn(array());
        $node->hasMethod(Argument::any())->willReturn(false);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);

        $this->apply($node);
    }

    function it_forces_all_class_methods_except_constructor_to_proxy_calls_into_prophecy_makeCall(
        ClassNode $node,
        MethodNode $constructor,
        MethodNode $method1,
        MethodNode $method2,
        MethodNode $method3
    ) {
        $node->addInterface('Prophecy\Prophecy\ProphecySubjectInterface')->willReturn(null);
        $node->addProperty('objectProphecy', 'private')->willReturn(null);
        $node->hasMethod(Argument::any())->willReturn(false);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);

        $constructor->getName()->willReturn('__construct');
        $method1->getName()->willReturn('method1');
        $method2->getName()->willReturn('method2');
        $method3->getName()->willReturn('method3');

        $method1->getReturnType()->willReturn('int');
        $method2->getReturnType()->willReturn('int');
        $method3->getReturnType()->willReturn('void');

        $node->getMethods()->willReturn(array(
            'method1' => $method1,
            'method2' => $method2,
            'method3' => $method3,
        ));

        $constructor->setCode(Argument::any())->shouldNotBeCalled();

        $method1->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method2->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method3->setCode('$this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();

        $this->apply($node);
    }
}
