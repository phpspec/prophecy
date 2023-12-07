<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy;

use Prophecy\Doubler\CachedDoubler;
use Prophecy\Doubler\Doubler;
use Prophecy\Doubler\LazyDouble;
use Prophecy\Doubler\ClassPatch;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\RevealerInterface;
use Prophecy\Prophecy\Revealer;
use Prophecy\Call\CallCenter;
use Prophecy\Util\StringUtil;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Exception\Prediction\AggregateException;

/**
 * Prophet creates prophecies.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Prophet
{
    /**
     * @var Doubler
     */
    private $doubler;
    private $revealer;
    private $util;

    /**
     * @var list<ObjectProphecy<object>>
     */
    private $prophecies = array();

    public function __construct(
        Doubler $doubler = null,
        RevealerInterface $revealer = null,
        StringUtil $util = null
    ) {
        if (null === $doubler) {
            $doubler = new CachedDoubler();
            $doubler->registerClassPatch(new ClassPatch\SplFileInfoPatch);
            $doubler->registerClassPatch(new ClassPatch\TraversablePatch);
            $doubler->registerClassPatch(new ClassPatch\ThrowablePatch);
            $doubler->registerClassPatch(new ClassPatch\DisableConstructorPatch);
            $doubler->registerClassPatch(new ClassPatch\ProphecySubjectPatch);
            $doubler->registerClassPatch(new ClassPatch\ReflectionClassNewInstancePatch);
            $doubler->registerClassPatch(new ClassPatch\MagicCallPatch);
            $doubler->registerClassPatch(new ClassPatch\KeywordPatch);
        }

        $this->doubler  = $doubler;
        $this->revealer = $revealer ?: new Revealer;
        $this->util     = $util ?: new StringUtil;
    }

    /**
     * Creates new object prophecy.
     *
     * @param null|string $classOrInterface Class or interface name
     *
     * @return ObjectProphecy
     *
     * @template T of object
     * @phpstan-param class-string<T>|null $classOrInterface
     * @phpstan-return ($classOrInterface is null ? ObjectProphecy<object> : ObjectProphecy<T>)
     */
    public function prophesize($classOrInterface = null)
    {
        $this->prophecies[] = $prophecy = new ObjectProphecy(
            new LazyDouble($this->doubler),
            new CallCenter($this->util),
            $this->revealer
        );

        if ($classOrInterface) {
            if (class_exists($classOrInterface)) {
                return $prophecy->willExtend($classOrInterface);
            }

            if (interface_exists($classOrInterface)) {
                return $prophecy->willImplement($classOrInterface);
            }

            throw new ClassNotFoundException(sprintf(
                'Cannot prophesize class %s, because it cannot be found.',
                $classOrInterface
            ), $classOrInterface);
        }

        return $prophecy;
    }

    /**
     * Returns all created object prophecies.
     *
     * @return list<ObjectProphecy<object>>
     */
    public function getProphecies()
    {
        return $this->prophecies;
    }

    /**
     * Returns Doubler instance assigned to this Prophet.
     *
     * @return Doubler
     */
    public function getDoubler()
    {
        return $this->doubler;
    }

    /**
     * Checks all predictions defined by prophecies of this Prophet.
     *
     * @return void
     *
     * @throws Exception\Prediction\AggregateException If any prediction fails
     */
    public function checkPredictions()
    {
        $exception = new AggregateException("Some predictions failed:\n");
        foreach ($this->prophecies as $prophecy) {
            try {
                $prophecy->checkProphecyMethodsPredictions();
            } catch (PredictionException $e) {
                $exception->append($e);
            }
        }

        if (count($exception->getExceptions())) {
            throw $exception;
        }
    }
}
