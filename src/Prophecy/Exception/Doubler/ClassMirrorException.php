<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Exception\Doubler;

use ReflectionClass;

class ClassMirrorException extends \RuntimeException implements DoublerException
{
    private $class;

    /**
     * @param string                  $message
     * @param ReflectionClass<object> $class
     */
    public function __construct($message, ReflectionClass $class)
    {
        parent::__construct($message);

        $this->class = $class;
    }

    /**
     * @return ReflectionClass<object>
     */
    public function getReflectedClass()
    {
        return $this->class;
    }
}
