<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\UnionType;

class DisableConstructorPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function its_priority_is_100()
    {
        $this->getPriority()->shouldReturn(100);
    }

    function it_supports_anything(ClassNode $node)
    {
        $this->supports($node)->shouldReturn(true);
    }

    function it_makes_all_constructor_arguments_optional(
        ClassNode $class,
        MethodNode $method,
        ArgumentNode $arg1,
        ArgumentNode $arg2,
        ArgumentNode $arg3
    ) {
        $arg1->getTypeNode()->willReturn(new ArgumentTypeNode(new UnionType([
            new BuiltinType('string'),
            new BuiltinType('null'),
        ])));
        $arg2->getTypeNode()->willReturn(new ArgumentTypeNode(new BuiltinType('mixed')));
        $arg3->getTypeNode()->willReturn(new ArgumentTypeNode(new BuiltinType('string')));

        $class->isExtendable('__construct')->willReturn(true);
        $class->hasMethod('__construct')->willReturn(true);
        $class->getMethod('__construct')->willReturn($method);
        $method->getArguments()->willReturn(array($arg1, $arg2, $arg3));

        $arg1->setDefault(null)->shouldBeCalled();
        $arg2->setDefault(null)->shouldBeCalled();
        $arg3->setDefault(null)->shouldBeCalled();

        $arg3->setTypeNode(new ArgumentTypeNode(new UnionType([new BuiltinType('null'), new BuiltinType('string')])))->shouldBeCalled();

        $method->setCode(Argument::type('string'))->shouldBeCalled();

        $this->apply($class);
    }

    function it_creates_new_constructor_if_object_has_none(ClassNode $class)
    {
        $class->isExtendable('__construct')->willReturn(true);
        $class->hasMethod('__construct')->willReturn(false);
        $class->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'))
            ->shouldBeCalled();

        $this->apply($class);
    }

    function it_ignores_final_constructor(ClassNode $class)
    {
        $class->isExtendable('__construct')->willReturn(false);

        $this->apply($class);
    }
}
