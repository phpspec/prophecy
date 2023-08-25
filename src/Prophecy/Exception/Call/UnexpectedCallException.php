<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Exception\Call;

use Prophecy\Exception\Prophecy\ObjectProphecyException;
use Prophecy\Prophecy\ObjectProphecy;

class UnexpectedCallException extends ObjectProphecyException
{
    private $methodName;
    private $arguments;

    /**
     * @param string                 $message
     * @param ObjectProphecy<object> $objectProphecy
     * @param string                 $methodName
     * @param array<mixed>           $arguments
     */
    public function __construct($message, ObjectProphecy $objectProphecy,
                                $methodName, array $arguments)
    {
        parent::__construct($message, $objectProphecy);

        $this->methodName = $methodName;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return array<mixed>
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
