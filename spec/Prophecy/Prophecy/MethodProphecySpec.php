<?php

namespace spec\Prophecy\Prophecy;

use PhpSpec\ObjectBehavior;

class MethodProphecySpec extends ObjectBehavior
{
    /**
     * @param Prophecy\Prophecy\ObjectProphecy $objectProphecy
     * @param ReflectionClass                  $reflection
     */
    function let($objectProphecy, $reflection)
    {
        $objectProphecy->reveal()->willReturn($reflection);

        $this->beConstructedWith($objectProphecy, 'getName', null);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Prophecy\MethodProphecy');
    }

    function its_constructor_throws_MethodNotFoundException_for_unexisting_method($objectProphecy)
    {
        $this->shouldThrow('Prophecy\Exception\Doubler\MethodNotFoundException')->during(
            '__construct', array($objectProphecy, 'getUnexisting', null)
        );
    }

    function its_constructor_transforms_array_passed_as_3rd_argument_to_ArgumentsWildcard(
        $objectProphecy
    )
    {
        $this->beConstructedWith($objectProphecy, 'getName', array(42, 33));

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldNotBe(null);
        $wildcard->__toString()->shouldReturn('exact(42), exact(33)');
    }

    function its_constructor_does_not_touch_third_argument_if_it_is_null($objectProphecy)
    {
        $this->beConstructedWith($objectProphecy, 'getName', null);

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldBe(null);
    }

    /**
     * @param Prophecy\Promise\PromiseInterface $promise
     */
    function it_records_promise_through_will_method($promise, $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->will($promise);
        $this->getPromise()->shouldReturn($promise);
    }

    /**
     * @param Prophecy\Promise\PromiseInterface $promise
     */
    function it_adds_itself_to_ObjectProphecy_during_call_to_will($objectProphecy, $promise)
    {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->will($promise);
    }

    function it_adds_ReturnPromise_during_willReturn_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturn(42);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnPromise');
    }

    function it_adds_ThrowPromise_during_willThrow_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willThrow('RuntimeException');
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ThrowPromise');
    }

    function it_adds_ReturnArgumentPromise_during_willReturnArgument_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturnArgument();
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnArgumentPromise');
    }

    function it_adds_CallbackPromise_during_will_call_with_callback_argument($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function(){};

        $this->will($callback);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\CallbackPromise');
    }

    /**
     * @param Prophecy\Prediction\PredictionInterface $prediction
     */
    function it_records_prediction_through_should_method($prediction, $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('should', array($prediction));
        $this->getPrediction()->shouldReturn($prediction);
    }

    function it_adds_CallbackPrediction_during_should_call_with_callback_argument($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function(){};

        $this->callOnWrappedObject('should', array($callback));
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallbackPrediction');
    }

    /**
     * @param Prophecy\Prediction\PredictionInterface $prediction
     */
    function it_adds_itself_to_ObjectProphecy_during_call_to_should($objectProphecy, $prediction)
    {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->callOnWrappedObject('should', array($prediction));
    }

    function it_adds_CallPrediction_during_shouldBeCalled_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalled', array());
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallPrediction');
    }

    function it_adds_NoCallsPrediction_during_shouldNotBeCalled_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldNotBeCalled', array());
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\NoCallsPrediction');
    }

    function it_adds_CallTimesPrediction_during_shouldBeCalledTimes_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalledTimes', array(5));
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallTimesPrediction');
    }

    /**
     * @param Prophecy\Argument\ArgumentsWildcard     $arguments
     * @param Prophecy\Prediction\PredictionInterface $prediction
     * @param Prophecy\Call\Call                      $call1
     * @param Prophecy\Call\Call                      $call2
     */
    function it_checks_prediction_via_shouldHave_method_call(
        $objectProphecy, $arguments, $prediction, $call1, $call2
    )
    {
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', array($prediction));
    }

    /**
     * @param Prophecy\Argument\ArgumentsWildcard     $arguments
     * @param Prophecy\Prediction\PredictionInterface $prediction
     * @param Prophecy\Call\Call                      $call1
     * @param Prophecy\Call\Call                      $call2
     */
    function it_checks_prediction_via_shouldHave_method_call_with_callback(
        $objectProphecy, $arguments, $prediction, $call1, $call2
    )
    {
        $callback = function($calls, $object, $method) {
            throw new \RuntimeException;
        };
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->withArguments($arguments);
        $this->shouldThrow('RuntimeException')->duringShouldHave($callback);
    }

    function it_does_nothing_during_checkPrediction_if_no_prediction_set()
    {
        $this->checkPrediction()->shouldReturn(null);
    }

    /**
     * @param Prophecy\Argument\ArgumentsWildcard     $arguments
     * @param Prophecy\Prediction\PredictionInterface $prediction
     * @param Prophecy\Call\Call                      $call1
     * @param Prophecy\Call\Call                      $call2
     */
    function it_checks_set_prediction_during_checkPrediction(
        $objectProphecy, $arguments, $prediction, $call1, $call2
    )
    {
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->withArguments($arguments);
        $this->callOnWrappedObject('should', array($prediction));
        $this->checkPrediction();
    }

    function it_links_back_to_ObjectProphecy_through_getter($objectProphecy)
    {
        $this->getObjectProphecy()->shouldReturn($objectProphecy);
    }

    function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    /**
     * @param Prophecy\Argument\ArgumentsWildcard $wildcard
     */
    function it_contains_ArgumentsWildcard_it_was_constructed_with($objectProphecy, $wildcard)
    {
        $this->beConstructedWith($objectProphecy, 'getName', $wildcard);

        $this->getArgumentsWildcard()->shouldReturn($wildcard);
    }

    /**
     * @param Prophecy\Argument\ArgumentsWildcard $wildcard
     */
    function its_ArgumentWildcard_is_mutable_through_setter($wildcard)
    {
        $this->withArguments($wildcard);

        $this->getArgumentsWildcard()->shouldReturn($wildcard);
    }

    function its_withArguments_transforms_passed_array_into_ArgumentsWildcard()
    {
        $this->withArguments(array(42, 33));

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->shouldNotBe(null);
        $wildcard->__toString()->shouldReturn('exact(42), exact(33)');
    }

    function its_withArguments_throws_exception_if_wrong_arguments_provided()
    {
        $this->shouldThrow('Prophecy\Exception\InvalidArgumentException')->duringWithArguments(42);
    }
}
