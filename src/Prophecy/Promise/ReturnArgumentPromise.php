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
 * Return argument promise.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ReturnArgumentPromise implements PromiseInterface
{
    /**
     * Returns first argument if has one, null otherwise.
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @return null|mixed
     */
    public function execute(array $args, ObjectProphecy $object, MethodProphecy $method)
    {
        return count($args) ? $args[0] : null;
    }
}
