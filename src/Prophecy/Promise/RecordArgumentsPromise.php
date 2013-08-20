<?php

namespace Prophecy\Promise;

use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\MethodProphecy;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Records argument promise.
 *
 * This promise stores the arguments passed during execution in the variable
 * provided during construction.
 *
 * @author Liam O'Boyle <liam@ontheroad.net.nz>
 */
class RecordArgumentsPromise implements PromiseInterface
{
    protected $storage;

    /**
     * Retrieve the stored arguments from the promise.
     *
     * @return mixed
     */
    public function getArguments() {
        return $this->storage;
    }

    /**
     * Evaluates promise callback.
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @return mixed
     */
    public function execute(array $args, ObjectProphecy $object, MethodProphecy $method)
    {
        $this->storage = $args;
    }
}
