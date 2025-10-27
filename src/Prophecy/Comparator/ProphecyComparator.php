<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Comparator;

use Prophecy\Prophecy\ProphecyInterface;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\Factory;
use SebastianBergmann\Comparator\ObjectComparator;

/**
 * @final
 */
class ProphecyComparator extends Comparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return \is_object($expected) && $actual instanceof ProphecyInterface;
    }

    /**
     * @param mixed $expected
     * @param mixed $actual
     * @param float $delta
     * @param bool  $canonicalize
     * @param bool  $ignoreCase
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false): void
    {
        \assert($actual instanceof ProphecyInterface);
        $this->getComparatorFactory()->getComparatorFor($expected, $actual->reveal())->assertEquals($expected, $actual->reveal(), $delta, $canonicalize, $ignoreCase);
    }

    private function getComparatorFactory(): Factory
    {
        // sebastianbergmann/comparator 5+
        // @phpstan-ignore function.alreadyNarrowedType
        if (\method_exists($this, 'factory')) {
            return $this->factory();
        }

        // sebastianbergmann/comparator <5
        // @phpstan-ignore property.private
        return $this->factory;
    }
}
