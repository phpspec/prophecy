<?php

namespace spec\Prophecy\Prediction;

use PHPSpec2\ObjectBehavior;

class CallPrediction extends ObjectBehavior
{
    function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    /**
     * @param Prophecy\Prophecy\ObjectProphecy $object
     * @param Prophecy\Prophecy\MethodProphecy $method
     * @param Prophecy\Prophecy\Call           $call
     */
    function it_does_nothing_if_there_is_more_than_one_call_been_made($object, $method, $call)
    {
        $this->check(array($call), $object, $method)->shouldReturn(null);
    }

    /**
     * @param Prophecy\Prophecy\ObjectProphecy    $object
     * @param Prophecy\Prophecy\MethodProphecy    $method
     * @param Prophecy\Argument\ArgumentsWildcard $arguments
     */
    function it_throws_NoCallsException_if_no_calls_found($object, $method, $arguments)
    {
        $method->getObjectProphecy()->willReturn($object);
        $method->getMethodName()->willReturn('getName');
        $method->getArgumentsWildcard()->willReturn($arguments);
        $arguments->__toString()->willReturn('123');

        $this->shouldThrow('Prophecy\Exception\Prediction\NoCallsException')
            ->duringCheck(array(), $object, $method);
    }
}
