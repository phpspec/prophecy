<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Exception\Prediction;

use Prophecy\Prophecy\ObjectProphecy;

class AggregateException extends \RuntimeException implements PredictionException
{
    /**
     * @var list<PredictionException>
     */
    private $exceptions = array();
    /**
     * @var ObjectProphecy<object>|null
     */
    private $objectProphecy;

    /**
     * @return void
     */
    public function append(PredictionException $exception)
    {
        $message = $exception->getMessage();
        $message = strtr($message, array("\n" => "\n  "))."\n";
        $message = empty($this->exceptions) ? $message : "\n".$message;

        $this->message      = rtrim($this->message.$message);
        $this->exceptions[] = $exception;
    }

    /**
     * @return list<PredictionException>
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param ObjectProphecy<object> $objectProphecy
     *
     * @return void
     */
    public function setObjectProphecy(ObjectProphecy $objectProphecy)
    {
        $this->objectProphecy = $objectProphecy;
    }

    /**
     * @return ObjectProphecy<object>|null
     */
    public function getObjectProphecy()
    {
        return $this->objectProphecy;
    }
}
