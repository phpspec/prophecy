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
 * Lazy Return promise.
 *
 * This promise will reveal the object only when executed
 *
 * @author Baptiste Clavié <clavie.b@gmail.com>
 */
class LazyRevealReturnPromise implements PromiseInterface
{
    private $prophecy;

    /** Initializes promise. */
    public function __construct(ObjectProphecy $prophecy)
    {
        $this->prophecy = $prophecy;
    }

    /**
     * Returns the revealed prophecy
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @return object
     */
    public function execute(array $args, ObjectProphecy $object, MethodProphecy $method)
    {
        return $this->prophecy->reveal();
    }
}
