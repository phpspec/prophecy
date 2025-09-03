<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Argument\Token;

/**
 * Check if values is not in array
 *
 * @author Vinícius Alonso <vba321@hotmail.com>
 */
class NotInArrayToken implements TokenInterface
{
    private $token = array();
    private $strict;

    /**
     * @param array<mixed> $arguments tokens
     * @param bool $strict
     */
    public function __construct(array $arguments, $strict = true)
    {
        $this->token = $arguments;
        $this->strict = $strict;
    }

    /**
     * Return scores 8 score if argument is in array.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (count($this->token) === 0) {
            return false;
        }

        if (!\in_array($argument, $this->token, $this->strict)) {
            return 8;
        }

        return false;
    }

    /**
     * Returns false.
     *
     * @return boolean
     */
    public function isLast()
    {
        return false;
    }

    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        $arrayAsString = implode(', ', $this->token);
        return "[{$arrayAsString}]";
    }
}
