<?php

namespace spec\Prophecy\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;


class LowerBoundaryCallPredictionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(2);
    }

    function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy $object
     * @param \Prophecy\Prophecy\MethodProphecy $method
     * @param \Prophecy\Call\Call               $call1
     * @param \Prophecy\Call\Call               $call2
     * @param \Prophecy\Call\Call               $call3
     */
    function it_does_nothing_if_there_were_a_lower_amount_of_calls_being_made(
        ObjectProphecy $object, MethodProphecy $method, $call1, $call2, $call3
    )
    {
        $this->check(array($call1, $call2), $object, $method)->shouldReturn(null);
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy $object
     * @param \Prophecy\Prophecy\MethodProphecy $method
     * @param \Prophecy\Call\Call               $call1
     */
    function it_does_nothing_if_there_were_an_equal_amount_of_calls_being_made(
        ObjectProphecy $object, MethodProphecy $method, $call1, $call2, $call3
    )
    {
        $this->check(array($call1, $call2), $object, $method)->shouldReturn(null);
    }

    /**
     * @param \Prophecy\Prophecy\ObjectProphecy    $object
     * @param \Prophecy\Prophecy\MethodProphecy    $method
     * @param \Prophecy\Call\Call                  $call
     * @param \Prophecy\Argument\ArgumentsWildcard $arguments
     */
    function it_throws_UnexpectedCallsCountException_if_calls_found(
        $object, $method, $call, $arguments
    )
    {
        $method->getObjectProphecy()->willReturn($object);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->__toString()->willReturn('123');

        $call->getMethodName()->willReturn('getName');
        $call->getArguments()->willReturn(array(5, 4, 'three'));
        $call->getCallPlace()->willReturn('unknown');

        $this->shouldThrow('Prophecy\Exception\Prediction\UnexpectedCallsCountException')
            ->duringCheck(array($call,$call,$call), $object, $method);
    }
}
