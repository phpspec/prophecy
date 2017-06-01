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

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;

/**
 * Return self promise.
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class ReturnSelfPromise implements PromiseInterface
{
    /**
     * Returns the object prophecy revealed
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @return ObjectProphecy
     */
    public function execute(array $args, ObjectProphecy $object, MethodProphecy $method)
    {
        return $object;
    }
}

