<?php

namespace Prophecy\Prediction;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Util\StringUtil;

use Prophecy\Exception\Prediction\UnexpectedCallsException;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * No calls prediction.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NoCallsPrediction implements PredictionInterface
{
    private $util;

    /**
     * Initializes prediction.
     *
     * @param null|StringUtil $util
     */
    public function __construct(StringUtil $util = null)
    {
        $this->util = $util ?: new StringUtil;
    }

    /**
     * Tests that there were no calls made.
     *
     * @param array          $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\UnexpectedCallsException
     */
    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method)
    {
        if (!count($calls)) {
            return;
        }

        $util   = $this->util;
        $actual = implode("\n", array_map(function($call) use($util) {
            return sprintf('- `%s(%s)` from %s',
                $call->getMethodName(),
                implode(', ', array_map(array($util, 'stringify'), $call->getArguments())),
                $call->getCallPlace()
            );
        }, $calls));

        throw new UnexpectedCallsException(sprintf(
            "No calls expected that match `%s->%s(%s)`, but some were made:\n%s",
            get_class($object->reveal()),
            $method->getMethodName(),
            $method->getArgumentsWildcard(),
            $actual
        ), $method, $calls);
    }
}
