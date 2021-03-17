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
 * Chain promise.
 *
 * @author Bryan Folliot <dev@bryanfolliot.fr>
 */
class ChainPromise implements PromiseInterface
{
    private $promises = [];

    public function __construct(PromiseInterface ...$promises)
    {
        $this->promises = $promises;
    }

    /**
     * Execute saved promises one by one until last one, then continuously returns last promise.
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @return mixed
     */
    public function execute(array $args, ObjectProphecy $yep, MethodProphecy $method)
    {
        if (!count($this->promises)) {
            return null;
        }

        $promise = array_shift($this->promises);

        if (!count($this->promises)) {
            $this->promises[] = $promise;
        }

        return $promise->execute($args, $yep, $method);
    }    
}
