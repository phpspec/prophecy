<?php

namespace spec\Prophecy\Prophecy;

use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Call\Call;
use Prophecy\Exception\Prophecy\MethodProphecyException;
use Prophecy\Prediction\PredictionInterface;
use Prophecy\Promise\CallbackPromise;
use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecySubjectInterface;
use ReflectionClass;
use RuntimeException;

class MethodProphecySpec extends ObjectBehavior
{
    function let(ObjectProphecy $objectProphecy, ReflectionClass $reflection, ArgumentsWildcard $argumentsWildcard)
    {
        $objectProphecy->reveal()->willReturn($reflection);

        if (\PHP_VERSION_ID >= 80100) {
            $objectProphecy->addMethodProphecy(Argument::any())->willReturn();
        }

        $this->beConstructedWith($objectProphecy, 'getName', $argumentsWildcard);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Prophecy\MethodProphecy');
    }

    function its_constructor_throws_MethodNotFoundException_for_unexisting_method(
        ObjectProphecy $objectProphecy,
        ObjectProphecy $objectProphecyInner,
        ReflectionClass $reflection,
        ArgumentsWildcard $argumentsWildcard
    ) {
        $objectProphecy->reveal()->willReturn($objectProphecyInner);
        $objectProphecyInner->reveal()->willReturn($reflection);
        $this->beConstructedWith($objectProphecy, 'getUnexisting', $argumentsWildcard);
        $this->shouldThrow('Prophecy\Exception\Doubler\MethodNotFoundException')->duringInstantiation();
    }

    function its_constructor_throws_MethodProphecyException_for_final_methods(
        ObjectProphecy $objectProphecy,
        ObjectProphecy $objectProphecyInner,
        ClassWithFinalMethod $subject
    ) {
        $objectProphecy->reveal()->willReturn($objectProphecyInner);
        $objectProphecyInner->reveal()->willReturn($subject);

        $this->shouldThrow('Prophecy\Exception\Prophecy\MethodProphecyException')->during(
            '__construct', array($objectProphecy, 'finalMethod', null)
        );
    }

    function its_constructor_transforms_array_passed_as_3rd_argument_to_ArgumentsWildcard(
        ObjectProphecy $objectProphecy
    ) {
        $this->beConstructedWith($objectProphecy, 'getName', array(42, 33));

        $wildcard = $this->getArgumentsWildcard();
        $wildcard->__toString()->shouldReturn('exact(42), exact(33)');
    }

    function its_constructor_records_default_callback_promise_for_return_type_hinted_methods(
        ObjectProphecy $objectProphecy,
        $subject,
        ArgumentsWildcard $argumentsWildcard
    ) {
        $subject->beADoubleOf('spec\Prophecy\Prophecy\ClassWithVoidTypeHintedMethods');
        $objectProphecy->addMethodProphecy(Argument::cetera())->willReturn(null);
        $objectProphecy->reveal()->willReturn($subject);

        $this->beConstructedWith($objectProphecy, 'getVoid', $argumentsWildcard);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\CallbackPromise');
    }

    function its_constructor_records_promise_that_returns_null_for_void_type_hinted_methods(
        ObjectProphecy $objectProphecy,
        $subject,
        ArgumentsWildcard $argumentsWildcard
    ) {
        $subject->beADoubleOf('spec\Prophecy\Prophecy\ClassWithVoidTypeHintedMethods');
        $objectProphecy->addMethodProphecy(Argument::cetera())->willReturn(null);
        $objectProphecy->reveal()->willReturn($subject);

        $this->beConstructedWith($objectProphecy, 'getVoid', $argumentsWildcard);
        $this->getPromise()->execute(array(), $objectProphecy, $this)->shouldBeNull();
    }

    function its_constructor_adds_itself_to_ObjectProphecy_for_return_type_hinted_methods(
        ObjectProphecy $objectProphecy,
        $subject,
        ArgumentsWildcard $argumentsWildcard
    ) {
        $subject->beADoubleOf('spec\Prophecy\Prophecy\ClassWithVoidTypeHintedMethods');
        $objectProphecy->addMethodProphecy(Argument::cetera())->willReturn(null);
        $objectProphecy->reveal()->willReturn($subject);

        $this->beConstructedWith($objectProphecy, 'getVoid', $argumentsWildcard);
        $objectProphecy->addMethodProphecy($this)->shouldHaveBeenCalled();
    }

    function it_records_promise_through_will_method(PromiseInterface $promise, ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->will($promise);
        $this->getPromise()->shouldReturn($promise);
    }

    function it_adds_itself_to_ObjectProphecy_during_call_to_will(
        ObjectProphecy $objectProphecy,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->will($promise);
    }

    function it_adds_ReturnPromise_during_willReturn_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturn(42);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnPromise');
    }

    function it_adds_CallbackPromise_during_willYield_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willYield(array('foo', 'bar'));
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\CallbackPromise');
    }

    function it_yields_elements_configured_in_willYield(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willYield(array('foo', 'bar'));
        $this->getPromise()->execute(array(), $objectProphecy, $this)->shouldYield(array('foo', 'bar'));
    }

    function it_yields_key_value_pairs_configured_in_willYield(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willYield(array(10 => 'foo', 11 => 'bar'));
        $this->getPromise()->execute(array(), $objectProphecy, $this)->shouldYield(array(10 => 'foo', 11 => 'bar'));
    }

    function it_yields_and_return_elements_configured_in_willYield(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willYield(array('foo', 'bar'), true);
        $generator = $this->getPromise()->execute(array(), $objectProphecy, $this);
        $generator->shouldYield(array('foo', 'bar'));
        $generator->callOnWrappedObject('getReturn')->shouldReturn(true);
    }

    function it_adds_ThrowPromise_during_willThrow_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willThrow('RuntimeException');
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ThrowPromise');
    }

    function it_adds_ReturnArgumentPromise_during_willReturnArgument_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturnArgument();
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\ReturnArgumentPromise');
    }

    function it_adds_ReturnArgumentPromise_during_willReturnArgument_call_with_index_argument(
        ObjectProphecy $objectProphecy
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->willReturnArgument(1);
        $promise = $this->getPromise();
        $promise->shouldBeAnInstanceOf('Prophecy\Promise\ReturnArgumentPromise');
        $promise->execute(array('one', 'two'), $objectProphecy, $this)->shouldReturn('two');
    }

    function it_adds_CallbackPromise_during_will_call_with_callback_argument(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function () {};

        $this->will($callback);
        $this->getPromise()->shouldBeAnInstanceOf('Prophecy\Promise\CallbackPromise');
    }

    function it_records_prediction_through_should_method(
        PredictionInterface $prediction,
        ObjectProphecy $objectProphecy
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('should', array($prediction));
        $this->getPrediction()->shouldReturn($prediction);
    }

    function it_adds_CallbackPrediction_during_should_call_with_callback_argument(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $callback = function () {};

        $this->callOnWrappedObject('should', array($callback));
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallbackPrediction');
    }

    function it_adds_itself_to_ObjectProphecy_during_call_to_should(
        ObjectProphecy $objectProphecy,
        PredictionInterface $prediction
    ) {
        $objectProphecy->addMethodProphecy($this)->shouldBeCalled();

        $this->callOnWrappedObject('should', array($prediction));
    }

    function it_adds_CallPrediction_during_shouldBeCalled_call($objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalled', array());
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallPrediction');
    }

    function it_adds_NoCallsPrediction_during_shouldNotBeCalled_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldNotBeCalled', array());
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\NoCallsPrediction');
    }

    function it_adds_CallTimesPrediction_during_shouldBeCalledTimes_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalledTimes', array(5));
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallTimesPrediction');
    }

    function it_adds_CallTimesPrediction_during_shouldBeCalledOnce_call(ObjectProphecy $objectProphecy)
    {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->callOnWrappedObject('shouldBeCalledOnce');
        $this->getPrediction()->shouldBeAnInstanceOf('Prophecy\Prediction\CallTimesPrediction');
    }

    function it_checks_prediction_via_shouldHave_method_call(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', array($prediction));
    }

    function it_sets_return_promise_during_shouldHave_call_if_none_was_set_before(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', array($prediction));

        $this->getPromise()->shouldReturnAnInstanceOf(
            \PHP_VERSION_ID < 80100
                ? 'Prophecy\Promise\ReturnPromise'
                : 'Prophecy\Promise\CallbackPromise'
        );
    }

    function it_does_not_set_return_promise_during_shouldHave_call_if_it_was_set_before(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->will($promise);
        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', array($prediction));

        $this->getPromise()->shouldReturn($promise);
    }

    function it_records_checked_predictions(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction1,
        PredictionInterface $prediction2,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction1->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->willReturn();
        $prediction2->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->willReturn();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->will($promise);
        $this->withArguments($arguments);
        $this->callOnWrappedObject('shouldHave', array($prediction1));
        $this->callOnWrappedObject('shouldHave', array($prediction2));

        $this->getCheckedPredictions()->shouldReturn(array($prediction1, $prediction2));
    }

    function it_records_even_failed_checked_predictions(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2,
        PromiseInterface $promise
    ) {
        $objectProphecy->addMethodProphecy($this)->willReturn(null);
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->willThrow(new RuntimeException());
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->will($promise);
        $this->withArguments($arguments);

        try {
            $this->callOnWrappedObject('shouldHave', array($prediction));
        } catch (\Exception $e) {}

        $this->getCheckedPredictions()->shouldReturn(array($prediction));
    }

    function it_checks_prediction_via_shouldHave_method_call_with_callback(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        Call $call1,
        Call $call2
    ) {
        $callback = function ($calls, $object, $method) {
            throw new RuntimeException;
        };
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));

        $this->withArguments($arguments);
        $this->shouldThrow('RuntimeException')->duringShouldHave($callback);
    }

    function it_does_nothing_during_checkPrediction_if_no_prediction_set()
    {
        $this->checkPrediction()->shouldReturn(null);
    }

    function it_checks_set_prediction_during_checkPrediction(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $arguments,
        PredictionInterface $prediction,
        Call $call1,
        Call $call2
    ) {
        $prediction->check(array($call1, $call2), $objectProphecy->getWrappedObject(), $this)->shouldBeCalled();
        $objectProphecy->findProphecyMethodCalls('getName', $arguments)->willReturn(array($call1, $call2));
        $objectProphecy->addMethodProphecy($this)->willReturn(null);

        $this->withArguments($arguments);
        $this->callOnWrappedObject('should', array($prediction));
        $this->checkPrediction();
    }

    function it_links_back_to_ObjectProphecy_through_getter(ObjectProphecy $objectProphecy)
    {
        $this->getObjectProphecy()->shouldReturn($objectProphecy);
    }

    function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    function it_contains_ArgumentsWildcard_it_was_constructed_with(
        ObjectProphecy $objectProphecy,
        ArgumentsWildcard $wildcard
    ) {
        $this->beConstructedWith($objectProphecy, 'getName', $wildcard);

        $this->getArgumentsWildcard()->shouldReturn($wildcard);
    }

    function its_ArgumentWildcard_is_mutable_through_setter(ArgumentsWildcard $wildcard)
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

    function it_returns_null_for_void_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'void');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBeNull();
    }

    function it_returns_empty_string_for_string_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'string');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe('');
    }

    function it_returns_zero_for_float_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'float');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe(0.00);
    }

    function it_returns_false_for_bool_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'bool');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe(false);
    }

    function it_returns_empty_for_array_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'array');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe([]);
    }

    function it_returns_empty_closure_for_callable_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'callable');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBeAnInstanceOf(\Closure::class);
    }

    function it_returns_empty_closure_for_closure_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'Closure');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBeAnInstanceOf(\Closure::class);
    }

    function it_returns_null_generator_for_traversable_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'Traversable');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldYieldLike([null]);
    }

    function it_returns_null_generator_for_generator_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'Generator');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldYieldLike([null]);
    }

    function it_returns_an_object_prophecy_for_other_object_return_types(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'ArrayObject');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $return = $this->getPromise()->execute([], $objectProphecy, $this);
        $return->shouldBeAnInstanceOf(\ArrayObject::class);
        $return->shouldImplement(ProphecySubjectInterface::class);
    }

    function it_returns_object_prophecy_for_nullable_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', '?ArrayObject');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $return = $this->getPromise()->execute([], $objectProphecy, $this);
        $return->shouldBeAnInstanceOf(\ArrayObject::class);
        $return->shouldImplement(ProphecySubjectInterface::class);
    }

    function it_returns_scalar_prophecy_for_scalar_and_null_union(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        if (\PHP_VERSION_ID < 80000) {
            return;
        }

        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'string|null|int');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldNotBeNull();
    }

    function it_returns_object_prophecy_for_object_scalar_union(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        if (\PHP_VERSION_ID < 80000) {
            return;
        }

        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'string|ArrayObject|int');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $return = $this->getPromise()->execute([], $objectProphecy, $this);
        $return->shouldBeAnInstanceOf(\ArrayObject::class);
        $return->shouldImplement(ProphecySubjectInterface::class);
    }

    function it_returns_object_prophecy_for_object_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'object');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $return = $this->getPromise()->execute([], $objectProphecy, $this);
        $return->shouldImplement(ProphecySubjectInterface::class);
    }

    function it_throws_for_non_existent_class_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'NonExistentClass');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $return = $this->getPromise()->shouldThrow(MethodProphecyException::class)->during('execute', [[], $objectProphecy, $this]);
    }

    function it_returns_true_prophecy_for_true_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        if (\PHP_VERSION_ID < 80200) {
            return;
        }

        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'true');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe(true);
    }

    function it_returns_false_prophecy_for_false_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        if (\PHP_VERSION_ID < 80200) {
            return;
        }

        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'false');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe(false);
    }

    function it_returns_null_prophecy_for_null_return_type(ObjectProphecy $objectProphecy, ArgumentsWildcard $argumentsWildcard)
    {
        if (\PHP_VERSION_ID < 80200) {
            return;
        }

        $this->generateMethodProphecyWithReturnValue($objectProphecy, 'foo', 'null');
        $this->beConstructedWith($objectProphecy, 'foo', $argumentsWildcard);

        $this->getPromise()->execute([], $objectProphecy, $this)->shouldBe(null);
    }

    private function generateMethodProphecyWithReturnValue($objectProphecy, string $methodName, string $returnType): void
    {
        $objectProphecy->reveal()->willReturn(
            eval(
            <<<CODE
return new class() {
     public function $methodName() : $returnType {}
};
CODE
            )
        );
        $objectProphecy->addMethodProphecy(Argument::any())->willReturn();
    }


}

class ClassWithFinalMethod
{
    final public function finalMethod() {}
}

class ClassWithVoidTypeHintedMethods
{
    public function getVoid(): void
    {
    }
}


