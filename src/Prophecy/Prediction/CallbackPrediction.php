<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Prediction;

use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Exception\InvalidArgumentException;
use Closure;
use ReflectionFunction;

/**
 * Executes preset callback.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallbackPrediction implements PredictionInterface
{
    private $callback;

    /**
     * @param callable $callback Custom callback
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf(
                'Callable expected as an argument to CallbackPrediction, but got %s.',
                gettype($callback)
            ));
        }

        $this->callback = $callback;
    }

    public function check(array $calls, ObjectProphecy $object, MethodProphecy $method)
    {
        $callback = $this->callback;

        if ($callback instanceof Closure && (new ReflectionFunction($callback))->getClosureThis() !== null) {
            $callback = Closure::bind($callback, $object) ?? $this->callback;
        }

        call_user_func($callback, $calls, $object, $method);
    }
}
