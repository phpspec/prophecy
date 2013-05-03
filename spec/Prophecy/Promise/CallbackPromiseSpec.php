<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;

class CallbackPromiseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('get_class');
    }

    function it_is_promise()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Promise\PromiseInterface');
    }

    /**
     * @param Prophecy\Prophecy\ObjectProphecy $object
     * @param Prophecy\Prophecy\MethodProphecy $method
     */
    function it_should_execute_callback($object, $method)
    {
        $firstArgumentCallback = function($args) {
            return $args[0];
        };

        $this->beConstructedWith($firstArgumentCallback);

        $this->execute(array('one', 'two'), $object, $method)->shouldReturn('one');
    }
}
