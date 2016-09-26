<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class ReturnArgumentPromiseSpec extends ObjectBehavior
{
    function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    function it_should_return_first_argument_if_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute(array('one', 'two'), $object, $method)->shouldReturn('one');
    }

    function it_should_return_null_if_no_arguments_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->execute(array(), $object, $method)->shouldReturn(null);
    }

    function it_should_return_nth_argument_if_provided(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith(1);
        $this->execute(array('one', 'two'), $object, $method)->shouldReturn('two');
    }
}
