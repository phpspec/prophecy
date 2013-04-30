<?php

namespace Prophecy\Exception\Prediction;

use Prophecy\Prophecy\ObjectProphecy;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AggregateException extends \RuntimeException implements PredictionException
{
    private $exceptions = array();
    private $objectProphecy;

    public function append(PredictionException $exception)
    {
        $message = $exception->getMessage();
        $message = '  '.strtr($message, array("\n" => "\n  "))."\n";

        $this->message      = rtrim($this->message.$message);
        $this->exceptions[] = $exception;
    }

    public function getExceptions()
    {
        return $this->exceptions;
    }

    public function setObjectProphecy(ObjectProphecy $objectProphecy)
    {
        $this->objectProphecy = $objectProphecy;
    }

    public function getObjectProphecy()
    {
        return $this->objectProphecy;
    }
}
