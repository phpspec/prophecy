<?php

namespace Prophecy\Prediction;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Util\StringUtil;

use Prophecy\Exception\Prediction\UnexpectedCallsCountException;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallTimesPrediction implements PredictionInterface
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
     * Tests that there was exact amount of calls made.
     *
     * @param array          $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\UnexpectedCallsCountException
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method)
    {
        if ($this->times == count($calls)) {
            return;
        }

        $message = sprintf(
            "Expected exactly %d calls that match `%s->%s(%s)`, but 0 were made.",
            $this->times,
            get_class($object->reveal()),
            $method->getMethodName(),
            $method->getArgumentsWildcard()
        );

        if (count($calls)) {
            $util   = $this->util;
            $actual = implode("\n", array_map(function($call) use($util) {
                return sprintf('- `%s(%s)` from %s',
                    $call->getMethodName(),
                    implode(', ', array_map(array($util, 'stringify'), $call->getArguments())),
                    $call->getCallPlace()
                );
            }, $calls));

            $message = sprintf(
                "Expected exactly %d calls that match `%s->%s(%s)`, but %d were made:\n%s",
                $this->times,
                get_class($object->reveal()),
                $method->getMethodName(),
                $method->getArgumentsWildcard(),
                count($calls),
                $actual
            );
        }

        throw new UnexpectedCallsCountException($message, $method, $this->times, $calls);
    }
}
