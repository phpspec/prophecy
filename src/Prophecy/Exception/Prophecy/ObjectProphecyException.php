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

use Prophecy\Prophecy\ObjectProphecy;

class ObjectProphecyException extends \RuntimeException implements ProphecyException
{
    private $objectProphecy;

    /**
     * @param string                 $message
     * @param ObjectProphecy<object> $objectProphecy
     */
    public function __construct($message, ObjectProphecy $objectProphecy, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->objectProphecy = $objectProphecy;
    }

    /**
     * @return ObjectProphecy<object>
     */
    public function getObjectProphecy()
    {
        return $this->objectProphecy;
    }
}
