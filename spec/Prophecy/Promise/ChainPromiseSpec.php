<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

class ChainPromiseSpec extends ObjectBehavior
{
    function let(PromiseInterface $firstPromise, PromiseInterface $secondPromise)
    {
        $this->beConstructedWith($firstPromise, $secondPromise);
    }

    function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    function it_executes_the_promises_it_was_constructed_with(
        ObjectProphecy $object,
        MethodProphecy $method,
        PromiseInterface $firstPromise,
        PromiseInterface $secondPromise
    ) {
        $this->execute([], $object, $method);
        $this->execute([], $object, $method);

        $firstPromise->execute([], Argument::any(), $method)->shouldHaveBeenCalled();
        $secondPromise->execute([], Argument::any(), $method)->shouldHaveBeenCalled();
    }

    function it_always_executes_the_last_one_promised_when_it_has_already_executed_the_others(
        ObjectProphecy $object,
        MethodProphecy $method,
        PromiseInterface $firstPromise,
        PromiseInterface $secondPromise
    ) {
        $this->execute([], $object, $method);
        $this->execute([], $object, $method);
        $this->execute([], $object, $method);
        $this->execute([], $object, $method);

        $firstPromise->execute([], Argument::any(), $method)->shouldHaveBeenCalled();
        $secondPromise->execute([], Argument::any(), $method)->shouldBeCalledTimes(3);
    }

    function it_returns_null_if_constructed_with_nothing(ObjectProphecy $object, MethodProphecy $method)
    {
        $this->beConstructedWith();

        $this->execute(array(), $object, $method)->shouldReturn(null);
    }
}
