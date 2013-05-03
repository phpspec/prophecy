<?php

namespace Prophecy\Argument\Token;

use Prophecy\Util\StringUtil;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Object state-checker token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ObjectStateToken implements TokenInterface
{
    private $methodName;
    private $value;
    private $util;

    /**
     * Initializes token.
     *
     * @param string          $methodName Checker method name
     * @param mixed           $value      Expected return value
     * @param null|StringUtil $util
     */
    public function __construct($methodName, $value, StringUtil $util = null)
    {
        $this->methodName = $methodName;
        $this->value      = $value;
        $this->util       = $util ?: new StringUtil;
    }

    /**
     * Scores 8 if argument is an object, which method returns expected value.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (is_object($argument) && method_exists($argument, $this->methodName)) {
            $actual = call_user_func(array($argument, $this->methodName));

            return $actual == $this->value ? 8 : false;
        }

        return false;
    }

    /**
     * Returns false.
     *
     * @return bool
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
        return sprintf('state(%s(), %s)',
            $this->methodName,
            $this->util->stringify($this->value)
        );
    }
}
