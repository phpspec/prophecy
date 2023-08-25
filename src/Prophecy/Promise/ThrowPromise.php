<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Promise;

use Doctrine\Instantiator\Instantiator;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Exception\InvalidArgumentException;
use ReflectionClass;

/**
 * Throws predefined exception.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ThrowPromise implements PromiseInterface
{
    private $exception;

    /**
     * @var Instantiator|null
     */
    private $instantiator;

    /**
     * Initializes promise.
     *
     * @param string|\Throwable $exception Exception class name or instance
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     *
     * @phpstan-param class-string<\Throwable>|\Throwable $exception
     */
    public function __construct($exception)
    {
        if (is_string($exception)) {
            if ((!class_exists($exception) && !interface_exists($exception)) || !$this->isAValidThrowable($exception)) {
                throw new InvalidArgumentException(sprintf(
                    'Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.',
                    $exception
                ));
            }
        } elseif (!$exception instanceof \Exception && !$exception instanceof \Throwable) {
            throw new InvalidArgumentException(sprintf(
                'Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.',
                is_object($exception) ? get_class($exception) : gettype($exception)
            ));
        }

        $this->exception = $exception;
    }

    public function execute(array $args, ObjectProphecy $object, MethodProphecy $method)
    {
        if (is_string($this->exception)) {
            $classname   = $this->exception;
            $reflection  = new ReflectionClass($classname);
            $constructor = $reflection->getConstructor();

            if ($constructor === null || $constructor->isPublic() && 0 == $constructor->getNumberOfRequiredParameters()) {
                throw $reflection->newInstance();
            }

            if (!$this->instantiator) {
                $this->instantiator = new Instantiator();
            }

            throw $this->instantiator->instantiate($classname);
        }

        throw $this->exception;
    }

    /**
     * @param string $exception
     *
     * @return bool
     */
    private function isAValidThrowable($exception)
    {
        return is_a($exception, 'Exception', true)
            || is_a($exception, 'Throwable', true);
    }
}
