<?php

namespace Prophecy\Prediction;

use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Argument\Token\AnyValuesToken;
use Prophecy\Util\StringUtil;
use Prophecy\Exception\Prediction\UnexpectedCallsCountException;

/**
 * Class UpperBoundaryCallPrediction implements an upper boundary call prediction.
 *
 * @author Francesco Panina <francesco.panina@gmail.com>
 */
class UpperBoundaryCallPrediction implements PredictionInterface
{
    private $times;
    private $util;

    /**
     * Initializes prediction.
     *
     * @param int        $times
     * @param StringUtil $util
     */
    public function __construct($times, StringUtil $util = null)
    {
        $this->times = intval($times);
        $this->util  = $util ?: new StringUtil;
    }

    /**
     * Tests that a at least a number of calls were made.
     *
     * @param Call[]         $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\UnexpectedCallsCountException
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method)
    {
        if (count($calls) >= $this->times) {
            return;
        }

        $methodCalls = $object->findProphecyMethodCalls(
            $method->getMethodName(),
            new ArgumentsWildcard(array(new AnyValuesToken))
        );

        if (count($calls)) {
            $message = sprintf(
                "Expected at least %d calls that match:\n".
                "  %s->%s(%s)\n".
                "but %d were made:\n%s",

                $this->times,
                get_class($object->reveal()),
                $method->getMethodName(),
                $method->getArgumentsWildcard(),
                count($calls),
                $this->util->stringifyCalls($calls)
            );
        } elseif (count($methodCalls)) {
            $message = sprintf(
                "Expected at least %d calls that match:\n".
                "  %s->%s(%s)\n".
                "but none were made.\n".
                "Recorded `%s(...)` calls:\n%s",

                $this->times,
                get_class($object->reveal()),
                $method->getMethodName(),
                $method->getArgumentsWildcard(),
                $method->getMethodName(),
                $this->util->stringifyCalls($methodCalls)
            );
        } else {
            $message = sprintf(
                "Expected at least %d calls that match:\n".
                "  %s->%s(%s)\n".
                "but none were made.",

                $this->times,
                get_class($object->reveal()),
                $method->getMethodName(),
                $method->getArgumentsWildcard()
            );
        }

        throw new UnexpectedCallsCountException($message, $method, $this->times, $calls);
    }
}

