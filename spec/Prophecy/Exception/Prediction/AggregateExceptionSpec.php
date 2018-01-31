<?php

namespace spec\Prophecy\Exception\Prediction;

use PhpSpec\ObjectBehavior;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Prophecy\ObjectProphecy;

class AggregateExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(null);
    }

    function it_is_prediction_exception()
    {
        $this->shouldBeAnInstanceOf('RuntimeException');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prediction\PredictionException');
    }

    function it_can_store_objectProphecy_link(ObjectProphecy $object)
    {
        $this->setObjectProphecy($object);
        $this->getObjectProphecy()->shouldReturn($object);
    }

    function it_should_not_have_exceptions_at_the_beginning()
    {
        $this->getExceptions()->shouldHaveCount(0);
    }

    function it_should_append_exception_through_append_method(PredictionException $exception)
    {
        $exception->getMessage()->willReturn('Exception #1');

        $this->append($exception);

        $this->getExceptions()->shouldReturn(array($exception));
    }

    function it_should_update_message_during_append(PredictionException $exception)
    {
        $exception->getMessage()->willReturn('Exception #1');

        $this->append($exception);

        $this->getMessage()->shouldReturn('Exception #1');
    }

    function it_should_update_message_during_append_more_exceptions(
        PredictionException $exception1,
        PredictionException $exception2
    ) {
        $exception1->getMessage()->willReturn('Exception #1');
        $exception2->getMessage()->willReturn('Exception #2');

        $this->append($exception1);
        $this->getMessage()->shouldReturn('Exception #1');

        $this->append($exception2);
        $this->getMessage()->shouldReturn("Exception #1\nException #2");
    }
}
