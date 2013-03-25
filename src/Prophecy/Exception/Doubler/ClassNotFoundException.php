<?php

namespace Prophecy\Exception\Doubler;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ClassNotFoundException extends DoubleException
{
    private $classname;

    public function __construct($message, $classname)
    {
        parent::__construct($message);

        $this->classname = $classname;
    }

    public function getClassname()
    {
        return $this->classname;
    }
}
