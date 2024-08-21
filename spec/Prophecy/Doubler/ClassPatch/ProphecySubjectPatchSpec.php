<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\Doubler\Generator\Node\PropertyTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;

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
        $node->addProperty(
            'objectProphecyClosureContainer',
            'private',
            new PropertyTypeNode('Prophecy\Doubler\ClassPatch\ProphecySubjectPatch\ObjectProphecyClosureContainer')
        )->willReturn(Argument::type('Prophecy\Doubler\ClassPatch\ProphecySubjectPatch\ObjectProphecyClosureContainer'));

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
        MethodNode $method3,
        MethodNode $method4
    ) {
        $node->addInterface('Prophecy\Prophecy\ProphecySubjectInterface')->willReturn(null);
        $node->addProperty(
            'objectProphecyClosureContainer',
            'private',
            new PropertyTypeNode('Prophecy\Doubler\ClassPatch\ProphecySubjectPatch\ObjectProphecyClosureContainer')
        )->willReturn(Argument::type('Prophecy\Doubler\ClassPatch\ProphecySubjectPatch\ObjectProphecyClosureContainer'));

        $node->hasMethod(Argument::any())->willReturn(false);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'), true)->willReturn(null);

        $constructor->getName()->willReturn('__construct');
        $method1->getName()->willReturn('method1');
        $method2->getName()->willReturn('method2');
        $method3->getName()->willReturn('method3');
        $method4->getName()->willReturn('method4');

        $method1->getReturnTypeNode()->willReturn(new ReturnTypeNode('int'));
        $method2->getReturnTypeNode()->willReturn(new ReturnTypeNode('int'));
        $method3->getReturnTypeNode()->willReturn(new ReturnTypeNode('void'));
        $method4->getReturnTypeNode()->willReturn(new ReturnTypeNode('never'));

        $node->getMethods()->willReturn(array(
            'method1' => $method1,
            'method2' => $method2,
            'method3' => $method3,
            'method4' => $method4,
        ));

        $constructor->setCode(Argument::any())->shouldNotBeCalled();

        $method1->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method2->setCode('return $this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method3->setCode('$this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();
        $method4->setCode('$this->getProphecy()->makeProphecyMethodCall(__FUNCTION__, func_get_args());')
            ->shouldBeCalled();

        $this->apply($node);
    }
}
