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

use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Closure comparator.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class ClosureComparator extends Comparator
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     */
    public function accepts($expected, $actual): bool
    {
        return is_object($expected) && $expected instanceof \Closure
            && is_object($actual) && $actual instanceof \Closure;
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
        if ($expected !== $actual) {
            // sebastian/comparator < 5 has a 6-param constructor with $identical (bool) as 5th param,
            // while >= 8 also has 6 params but with $contextLines (int) as 6th param.
            // Checking the parameter name avoids passing wrong types to the wrong signature.
            $r = new \ReflectionMethod(ComparisonFailure::class, '__construct');
            if ($r->getNumberOfParameters() >= 6 && $r->getParameters()[4]->getName() === 'identical') {
                // @phpstan-ignore-next-line
                throw new ComparisonFailure($expected, $actual, '', '', false, 'all closures are different if not identical');
            }

            throw new ComparisonFailure(
                $expected,
                $actual,
                // we don't need a diff
                '',
                '',
                'all closures are different if not identical'
            );
        }
    }
}
