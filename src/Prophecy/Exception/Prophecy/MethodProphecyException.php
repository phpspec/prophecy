<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Exception\Prophecy;

use Prophecy\Prophecy\MethodProphecy;

class MethodProphecyException extends ObjectProphecyException
{
    private $methodProphecy;

    /**
     * @param string $message
     */
    public function __construct($message, MethodProphecy $methodProphecy, ?\Throwable $previous = null)
    {
        parent::__construct($message, $methodProphecy->getObjectProphecy(), $previous);

        $this->methodProphecy = $methodProphecy;
    }

    /**
     * @return MethodProphecy
     */
    public function getMethodProphecy()
    {
        return $this->methodProphecy;
    }
}
