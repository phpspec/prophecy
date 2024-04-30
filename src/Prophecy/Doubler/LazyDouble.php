<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler;

use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use ReflectionClass;

/**
 * Lazy double.
 * Gives simple interface to describe double before creating it.
 *
 * @template T of object
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class LazyDouble
{
    private $doubler;
    /**
     * @var ReflectionClass<T>|null
     */
    private $class;
    /**
     * @var list<ReflectionClass<object>>
     */
    private $interfaces = array();
    /**
     * @var array<mixed>|null
     */
    private $arguments  = null;
    /**
     * @var (T&DoubleInterface)|null
     */
    private $double;

    public function __construct(Doubler $doubler)
    {
        $this->doubler = $doubler;
    }

    /**
     * Tells doubler to use specific class as parent one for double.
     *
     * @param class-string|ReflectionClass<object> $class
     *
     * @return void
     *
     * @template U of object
     * @phpstan-param class-string<U>|ReflectionClass<U> $class
     * @phpstan-this-out static<U>
     *
     * @throws ClassNotFoundException
     * @throws DoubleException
     */
    public function setParentClass($class)
    {
        if (null !== $this->double) {
            throw new DoubleException('Can not extend class with already instantiated double.');
        }

        if (!$class instanceof ReflectionClass) {
            if (!class_exists($class)) {
                throw new ClassNotFoundException(sprintf('Class %s not found.', $class), $class);
            }

            $class = new ReflectionClass($class);
        }

        /** @var static<U> $this */

        $this->class = $class;
    }

    /**
     * Tells doubler to implement specific interface with double.
     *
     * @param class-string|ReflectionClass<object> $interface
     *
     * @return void
     *
     * @template U of object
     * @phpstan-param class-string<U>|ReflectionClass<U> $interface
     * @phpstan-this-out static<T&U>
     *
     * @throws InterfaceNotFoundException
     * @throws DoubleException
     */
    public function addInterface($interface)
    {
        if (null !== $this->double) {
            throw new DoubleException(
                'Can not implement interface with already instantiated double.'
            );
        }

        if (!$interface instanceof ReflectionClass) {
            if (!interface_exists($interface)) {
                throw new InterfaceNotFoundException(
                    sprintf('Interface %s not found.', $interface),
                    $interface
                );
            }

            $interface = new ReflectionClass($interface);
        }

        $this->interfaces[] = $interface;
    }

    /**
     * Sets constructor arguments.
     *
     * @param array<mixed>|null $arguments
     *
     * @return void
     */
    public function setArguments(?array $arguments = null)
    {
        $this->arguments = $arguments;
    }

    /**
     * Creates double instance or returns already created one.
     *
     * @return T&DoubleInterface
     */
    public function getInstance()
    {
        if (null === $this->double) {
            if (null !== $this->arguments) {
                return $this->double = $this->doubler->double(
                    $this->class, $this->interfaces, $this->arguments
                );
            }

            $this->double = $this->doubler->double($this->class, $this->interfaces);
        }

        return $this->double;
    }
}
