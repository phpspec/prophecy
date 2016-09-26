<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\Generator\Node\MethodNode;

class SplFileInfoPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function its_priority_is_50()
    {
        $this->getPriority()->shouldReturn(50);
    }

    function it_does_not_support_nodes_without_parent_class(ClassNode $node)
    {
        $node->getParentClass()->willReturn('stdClass');
        $this->supports($node)->shouldReturn(false);
    }

    function it_supports_nodes_with_SplFileInfo_as_parent_class(ClassNode $node)
    {
        $node->getParentClass()->willReturn('SplFileInfo');
        $this->supports($node)->shouldReturn(true);
    }

    function it_supports_nodes_with_derivative_of_SplFileInfo_as_parent_class(ClassNode $node)
    {
        $node->getParentClass()->willReturn('SplFileInfo');
        $this->supports($node)->shouldReturn(true);
    }

    function it_adds_a_method_to_node_if_not_exists(ClassNode $node)
    {
        $node->hasMethod('__construct')->willReturn(false);
        $node->addMethod(Argument::any())->shouldBeCalled();
        $node->getParentClass()->shouldBeCalled();

        $this->apply($node);
    }

    function it_updates_existing_method_if_found(ClassNode $node, MethodNode $method)
    {
        $node->hasMethod('__construct')->willReturn(true);
        $node->getMethod('__construct')->willReturn($method);
        $node->getParentClass()->shouldBeCalled();

        $method->useParentCode()->shouldBeCalled();

        $this->apply($node);
    }

    function it_should_not_supply_a_file_for_a_directory_iterator(ClassNode $node, MethodNode $method)
    {
        $node->hasMethod('__construct')->willReturn(true);
        $node->getMethod('__construct')->willReturn($method);
        $node->getParentClass()->willReturn('DirectoryIterator');

        $method->setCode(Argument::that(function($value) {
            return strpos($value, '.php') === false;
        }))->shouldBeCalled();

        $this->apply($node);
    }

    function it_should_supply_a_file_for_a_spl_file_object(ClassNode $node, MethodNode $method)
    {
        $node->hasMethod('__construct')->willReturn(true);
        $node->getMethod('__construct')->willReturn($method);
        $node->getParentClass()->willReturn('SplFileObject');

        $method->setCode(Argument::that(function($value) {
            return strpos($value, '.php') !== false;
        }))->shouldBeCalled();

        $this->apply($node);
    }
}
