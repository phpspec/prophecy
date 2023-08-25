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

use SebastianBergmann\Comparator\Factory;

/**
 * Prophecy comparator factory.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class FactoryProvider
{
    /**
     * @var Factory|null
     */
    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): Factory
    {
        if (self::$instance === null) {
            self::$instance = new Factory();
            self::$instance->register(new ClosureComparator());
            self::$instance->register(new ProphecyComparator());
        }

        return self::$instance;
    }
}
