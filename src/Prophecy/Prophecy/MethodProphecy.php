<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Prophecy;

use Prophecy\Argument;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Prophet;
use Prophecy\Promise;
use Prophecy\Prediction;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Prophecy\MethodProphecyException;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Method prophecy.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class MethodProphecy
{
    private $objectProphecy;
    private $methodName;
    /**
     * @var Argument\ArgumentsWildcard
     */
    private $argumentsWildcard;
    /**
     * @var Promise\PromiseInterface|null
     */
    private $promise;
    /**
     * @var Prediction\PredictionInterface|null
     */
    private $prediction;
    /**
     * @var list<Prediction\PredictionInterface>
     */
    private $checkedPredictions = array();
    /**
     * @var bool
     */
    private $bound = false;
    /**
     * @var bool
     */
    private $voidReturnType = false;

    /**
     * @param ObjectProphecy<object>                  $objectProphecy
     * @param string                                  $methodName
     * @param Argument\ArgumentsWildcard|array<mixed> $arguments
     *
     * @throws \Prophecy\Exception\Doubler\MethodNotFoundException If method not found
     *
     * @internal
     */
    public function __construct(ObjectProphecy $objectProphecy, $methodName, $arguments)
    {
        $double = $objectProphecy->reveal();
        if (!method_exists($double, $methodName)) {
            throw new MethodNotFoundException(sprintf(
                'Method `%s::%s()` is not defined.', get_class($double), $methodName
            ), get_class($double), $methodName, $arguments);
        }

        $this->objectProphecy = $objectProphecy;
        $this->methodName     = $methodName;

        $reflectedMethod = new \ReflectionMethod($double, $methodName);
        if ($reflectedMethod->isFinal()) {
            throw new MethodProphecyException(sprintf(
                "Can not add prophecy for a method `%s::%s()`\n".
                "as it is a final method.",
                get_class($double),
                $methodName
            ), $this);
        }

        $this->withArguments($arguments);

        $hasTentativeReturnType = method_exists($reflectedMethod, 'hasTentativeReturnType')
            && $reflectedMethod->hasTentativeReturnType();

        if (true === $reflectedMethod->hasReturnType() || $hasTentativeReturnType) {
            if ($hasTentativeReturnType) {
                $reflectionType = $reflectedMethod->getTentativeReturnType();
            } else {
                $reflectionType = $reflectedMethod->getReturnType();
            }

            if ($reflectionType instanceof ReflectionNamedType) {
                $types = [$reflectionType];
            } elseif ($reflectionType instanceof ReflectionUnionType) {
                $types = $reflectionType->getTypes();
            } else {
                throw new MethodProphecyException(sprintf(
                    "Can not add prophecy for a method `%s::%s()`\nas its return type is not supported by Prophecy yet.",
                    get_class($double),
                    $methodName
                ), $this);
            }

            $types = array_map(
                function (ReflectionNamedType $type) { return $type->getName(); },
                $types
            );

            usort(
                $types,
                static function (string $type1, string $type2) {

                    // null is lowest priority
                    if ($type2 == 'null') {
                        return -1;
                    } elseif ($type1 == 'null') {
                        return 1;
                    }

                    // objects are higher priority than scalars
                    $isObject = static function ($type) {
                        return class_exists($type) || interface_exists($type);
                    };

                    if ($isObject($type1) && !$isObject($type2)) {
                        return -1;
                    } elseif (!$isObject($type1) && $isObject($type2)) {
                        return 1;
                    }

                    // don't sort both-scalars or both-objects
                    return 0;
                }
            );

            $defaultType = $types[0];

            if ('void' === $defaultType) {
                $this->voidReturnType = true;
            }

            $this->will(function ($args, ObjectProphecy $object, MethodProphecy $method) use ($defaultType) {
                switch ($defaultType) {
                    case 'void': return;
                    case 'string': return '';
                    case 'float':  return 0.0;
                    case 'int':    return 0;
                    case 'bool':   return false;
                    case 'array':  return array();
                    case 'true': return true;
                    case 'false': return false;
                    case 'null': return null;

                    case 'callable':
                    case 'Closure':
                        return function () {};

                    case 'Traversable':
                    case 'Generator':
                        return (function () { yield; })();

                    case 'object':
                        $prophet = new Prophet();
                        return $prophet->prophesize()->reveal();

                    default:
                        if (!class_exists($defaultType) && !interface_exists($defaultType)) {
                            throw new MethodProphecyException(sprintf('Cannot create a return value for the method as the type "%s" is not supported. Configure an explicit return value instead.', $defaultType), $method);
                        }

                        $prophet = new Prophet();
                        return $prophet->prophesize($defaultType)->reveal();
                }
            });
        }
    }

    /**
     * Sets argument wildcard.
     *
     * @param array<mixed>|Argument\ArgumentsWildcard $arguments
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function withArguments($arguments)
    {
        if (is_array($arguments)) {
            $arguments = new Argument\ArgumentsWildcard($arguments);
        }

        if (!$arguments instanceof Argument\ArgumentsWildcard) {
            throw new InvalidArgumentException(sprintf(
                "Either an array or an instance of ArgumentsWildcard expected as\n".
                'a `MethodProphecy::withArguments()` argument, but got %s.',
                gettype($arguments)
            ));
        }

        $this->argumentsWildcard = $arguments;

        return $this;
    }

    /**
     * Sets custom promise to the prophecy.
     *
     * @param callable|Promise\PromiseInterface $promise
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function will($promise)
    {
        if (is_callable($promise)) {
            $promise = new Promise\CallbackPromise($promise);
        }

        if (!$promise instanceof Promise\PromiseInterface) {
            throw new InvalidArgumentException(sprintf(
                'Expected callable or instance of PromiseInterface, but got %s.',
                gettype($promise)
            ));
        }

        $this->bindToObjectProphecy();
        $this->promise = $promise;

        return $this;
    }

    /**
     * Sets return promise to the prophecy.
     *
     * @see \Prophecy\Promise\ReturnPromise
     *
     * @param mixed ...$return a list of return values
     *
     * @return $this
     */
    public function willReturn(...$return)
    {
        if ($this->voidReturnType) {
            throw new MethodProphecyException(
                "The method \"$this->methodName\" has a void return type, and so cannot return anything",
                $this
            );
        }

        return $this->will(new Promise\ReturnPromise($return));
    }

    /**
     * @param array<mixed> $items
     * @param mixed $return
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function willYield($items, $return = null)
    {
        if ($this->voidReturnType) {
            throw new MethodProphecyException(
                "The method \"$this->methodName\" has a void return type, and so cannot yield anything",
                $this
            );
        }

        if (!is_array($items)) {
            throw new InvalidArgumentException(sprintf(
                'Expected array, but got %s.',
                gettype($items)
            ));
        }

        $generator =  function () use ($items, $return) {
            yield from $items;

            return $return;
        };

        return $this->will($generator);
    }

    /**
     * Sets return argument promise to the prophecy.
     *
     * @param int $index The zero-indexed number of the argument to return
     *
     * @see \Prophecy\Promise\ReturnArgumentPromise
     *
     * @return $this
     */
    public function willReturnArgument($index = 0)
    {
        if ($this->voidReturnType) {
            throw new MethodProphecyException("The method \"$this->methodName\" has a void return type", $this);
        }

        return $this->will(new Promise\ReturnArgumentPromise($index));
    }

    /**
     * Sets throw promise to the prophecy.
     *
     * @see \Prophecy\Promise\ThrowPromise
     *
     * @param string|\Throwable $exception Exception class or instance
     *
     * @return $this
     *
     * @phpstan-param class-string<\Throwable>|\Throwable $exception
     */
    public function willThrow($exception)
    {
        return $this->will(new Promise\ThrowPromise($exception));
    }

    /**
     * Sets custom prediction to the prophecy.
     *
     * @param callable|Prediction\PredictionInterface $prediction
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function should($prediction)
    {
        if (is_callable($prediction)) {
            $prediction = new Prediction\CallbackPrediction($prediction);
        }

        if (!$prediction instanceof Prediction\PredictionInterface) {
            throw new InvalidArgumentException(sprintf(
                'Expected callable or instance of PredictionInterface, but got %s.',
                gettype($prediction)
            ));
        }

        $this->bindToObjectProphecy();
        $this->prediction = $prediction;

        return $this;
    }

    /**
     * Sets call prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallPrediction
     *
     * @return $this
     */
    public function shouldBeCalled()
    {
        return $this->should(new Prediction\CallPrediction());
    }

    /**
     * Sets no calls prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     *
     * @return $this
     */
    public function shouldNotBeCalled()
    {
        return $this->should(new Prediction\NoCallsPrediction());
    }

    /**
     * Sets call times prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @param int $count
     *
     * @return $this
     */
    public function shouldBeCalledTimes($count)
    {
        return $this->should(new Prediction\CallTimesPrediction($count));
    }

    /**
     * Sets call times prediction to the prophecy.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @return $this
     */
    public function shouldBeCalledOnce()
    {
        return $this->shouldBeCalledTimes(1);
    }

    /**
     * Checks provided prediction immediately.
     *
     * @param callable|Prediction\PredictionInterface $prediction
     *
     * @return $this
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     * @throws PredictionException
     */
    public function shouldHave($prediction)
    {
        if (is_callable($prediction)) {
            $prediction = new Prediction\CallbackPrediction($prediction);
        }

        if (!$prediction instanceof Prediction\PredictionInterface) {
            throw new InvalidArgumentException(sprintf(
                'Expected callable or instance of PredictionInterface, but got %s.',
                gettype($prediction)
            ));
        }

        if (null === $this->promise && !$this->voidReturnType) {
            $this->willReturn();
        }

        $calls = $this->getObjectProphecy()->findProphecyMethodCalls(
            $this->getMethodName(),
            $this->getArgumentsWildcard()
        );

        try {
            $prediction->check($calls, $this->getObjectProphecy(), $this);
            $this->checkedPredictions[] = $prediction;
        } catch (\Exception $e) {
            $this->checkedPredictions[] = $prediction;

            throw $e;
        }

        return $this;
    }

    /**
     * Checks call prediction.
     *
     * @see \Prophecy\Prediction\CallPrediction
     *
     * @return $this
     *
     * @throws PredictionException
     */
    public function shouldHaveBeenCalled()
    {
        return $this->shouldHave(new Prediction\CallPrediction());
    }

    /**
     * Checks no calls prediction.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     *
     * @return $this
     *
     * @throws PredictionException
     */
    public function shouldNotHaveBeenCalled()
    {
        return $this->shouldHave(new Prediction\NoCallsPrediction());
    }

    /**
     * Checks no calls prediction.
     *
     * @see \Prophecy\Prediction\NoCallsPrediction
     * @deprecated
     *
     * @return $this
     */
    public function shouldNotBeenCalled()
    {
        return $this->shouldNotHaveBeenCalled();
    }

    /**
     * Checks call times prediction.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @param int $count
     *
     * @return $this
     */
    public function shouldHaveBeenCalledTimes($count)
    {
        return $this->shouldHave(new Prediction\CallTimesPrediction($count));
    }

    /**
     * Checks call times prediction.
     *
     * @see \Prophecy\Prediction\CallTimesPrediction
     *
     * @return $this
     */
    public function shouldHaveBeenCalledOnce()
    {
        return $this->shouldHaveBeenCalledTimes(1);
    }

    /**
     * Checks currently registered [with should(...)] prediction.
     *
     * @return void
     *
     * @throws PredictionException
     */
    public function checkPrediction()
    {
        if (null === $this->prediction) {
            return;
        }

        $this->shouldHave($this->prediction);
    }

    /**
     * Returns currently registered promise.
     *
     * @return null|Promise\PromiseInterface
     */
    public function getPromise()
    {
        return $this->promise;
    }

    /**
     * Returns currently registered prediction.
     *
     * @return null|Prediction\PredictionInterface
     */
    public function getPrediction()
    {
        return $this->prediction;
    }

    /**
     * Returns predictions that were checked on this object.
     *
     * @return list<Prediction\PredictionInterface>
     */
    public function getCheckedPredictions()
    {
        return $this->checkedPredictions;
    }

    /**
     * Returns object prophecy this method prophecy is tied to.
     *
     * @return ObjectProphecy<object>
     */
    public function getObjectProphecy()
    {
        return $this->objectProphecy;
    }

    /**
     * Returns method name.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Returns arguments wildcard.
     *
     * @return Argument\ArgumentsWildcard
     */
    public function getArgumentsWildcard()
    {
        return $this->argumentsWildcard;
    }

    /**
     * @return bool
     */
    public function hasReturnVoid()
    {
        return $this->voidReturnType;
    }

    /**
     * @return void
     */
    private function bindToObjectProphecy()
    {
        if ($this->bound) {
            return;
        }

        $this->getObjectProphecy()->addMethodProphecy($this);
        $this->bound = true;
    }
}
