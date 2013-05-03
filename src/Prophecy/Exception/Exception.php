<?php

namespace Prophecy\Exception;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Core Prophecy exception interface.
 * All Prophecy exceptions implement it.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface Exception
{
    /**
     * @return string
     */
    public function getMessage();
}
