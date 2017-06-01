<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class LazyRevealReturnPromiseSpec extends ObjectBehavior
{
    function let(ObjectProphecy $prophecy)
    {
        $this->beConstructedWith($prophecy);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Promise\LazyRevealReturnPromise');
    }

    function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy $object
     * @param \Prophecy\Prophecy\MethodProphecy $method
     */
    function it_always_returns_revealed_prophecy($object, $method, ObjectProphecy $prophecy)
    {
        $prophecy->reveal()->willReturn((object) array());

        $this->execute(array(), $object, $method)->shouldBeAnInstanceOf('stdClass');
    }
}
