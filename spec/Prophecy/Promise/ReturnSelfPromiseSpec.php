<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReturnSelfPromiseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Promise\ReturnSelfPromise');
    }

    function it_is_a_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy $object
     * @param \Prophecy\Prophecy\MethodProphecy $method
     */
    function it_should_return_object_prophecy_used($object, $method)
    {
        $this->execute(array(), $object, $method)->shouldReturn($object);
    }
}
