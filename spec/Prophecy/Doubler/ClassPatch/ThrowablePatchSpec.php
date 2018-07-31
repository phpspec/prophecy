<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\Exception\Example\SkippingException;
use Prophecy\Doubler\ClassPatch\ThrowablePatch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Doubler\Generator\Node\ClassNode;

class ThrowablePatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    function it_does_not_support_class_that_does_not_implement_throwable(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getInterfaces()->willReturn(array());
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(false);
    }

    function it_supports_class_that_extends_not_throwable_class(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getInterfaces()->willReturn(array('Throwable'));
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(true);
    }

    function it_does_not_support_class_that_already_extends_a_throwable_class(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getInterfaces()->willReturn(array('Throwable'));
        $node->getParentClass()->willReturn('InvalidArgumentException');

        $this->supports($node)->shouldReturn(false);
    }

    function it_supports_class_implementing_interface_that_extends_throwable(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getInterfaces()->willReturn(array('Fixtures\Prophecy\ThrowableInterface'));
        $node->getParentClass()->willReturn('stdClass');

        $this->supports($node)->shouldReturn(true);
    }

    function it_sets_the_parent_class_to_exception(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getParentClass()->willReturn('stdClass');

        $node->setParentClass('Exception')->shouldBeCalled();

        $node->removeMethod('getMessage')->shouldBeCalled();
        $node->removeMethod('getCode')->shouldBeCalled();
        $node->removeMethod('getFile')->shouldBeCalled();
        $node->removeMethod('getLine')->shouldBeCalled();
        $node->removeMethod('getTrace')->shouldBeCalled();
        $node->removeMethod('getPrevious')->shouldBeCalled();
        $node->removeMethod('getNext')->shouldBeCalled();
        $node->removeMethod('getTraceAsString')->shouldBeCalled();

        $this->apply($node);
    }

    function it_throws_error_when_trying_to_double_concrete_class_and_throwable_interface(ClassNode $node)
    {
        if (\PHP_VERSION_ID < 70000) {
            throw new SkippingException('Throwable is not defined in PHP 5');
        }

        $node->getParentClass()->willReturn('ArrayObject');

        $this->shouldThrow('Prophecy\Exception\Doubler\ClassCreatorException')->duringApply($node);
    }
}
