<?php

namespace spec\Prophecy\Promise;

use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class ThrowPromiseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('RuntimeException');
    }

    function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    function it_instantiates_and_throws_exception_from_provided_classname(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith('InvalidArgumentException');

        $this->shouldThrow('InvalidArgumentException')
            ->duringExecute(array(), $object, $method);
    }

    function it_instantiates_exceptions_with_required_arguments(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith('spec\Prophecy\Promise\RequiredArgumentException');

        $this->shouldThrow('spec\Prophecy\Promise\RequiredArgumentException')
            ->duringExecute(array(), $object, $method);
    }

    function it_throws_provided_exception(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith($exc = new \RuntimeException('Some exception'));

        $this->shouldThrow($exc)->duringExecute(array(), $object, $method);
    }

    function it_throws_error_instances(ObjectProphecy $object, MethodProphecy $method)
    {
        if (!class_exists('\Error')) {
            throw new SkippingException('The class Error, introduced in PHP 7, does not exist');
        }

        $this->beConstructedWith($exc = new \Error('Error exception'));

        $this->shouldThrow($exc)->duringExecute(array(), $object, $method);
    }

    function it_throws_errors_by_class_name()
    {
        if (!class_exists('\Error')) {
            throw new SkippingException('The class Error, introduced in PHP 7, does not exist');
        }

        $this->beConstructedWith('\Error');

        $this->shouldNotThrow('Prophecy\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_does_not_throw_something_that_is_not_throwable_by_class_name()
    {
        $this->beConstructedWith('\stdClass');

        $this->shouldThrow('Prophecy\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_does_not_throw_something_that_is_not_throwable_by_instance()
    {
        $this->beConstructedWith(new \stdClass());

        $this->shouldThrow('Prophecy\Exception\InvalidArgumentException')->duringInstantiation();
    }

    function it_throws_an_exception_by_class_name()
    {
        $this->beConstructedWith('\Exception');

        $this->shouldNotThrow('Prophecy\Exception\InvalidArgumentException')->duringInstantiation();
    }
}

class RequiredArgumentException extends \Exception
{
    final public function __construct($message, $code) {}
}
