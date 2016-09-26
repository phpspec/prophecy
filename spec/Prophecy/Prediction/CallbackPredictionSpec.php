<?php

namespace spec\Prophecy\Prediction;

use PhpSpec\ObjectBehavior;

use Prophecy\Call\Call;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException;

class CallbackPredictionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('get_class');
    }

    function it_is_prediction()
    {
        $this->shouldHaveType('Prophecy\Prediction\PredictionInterface');
    }

    function it_proxies_call_to_callback(ObjectProphecy $object, MethodProphecy $method, Call $call)
    {
        $returnFirstCallCallback = function ($calls, $object, $method) {
            throw new RuntimeException;
        };

        $this->beConstructedWith($returnFirstCallCallback);

        $this->shouldThrow('RuntimeException')->duringCheck(array($call), $object, $method);
    }
}
