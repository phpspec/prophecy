<?php

namespace Prophecy;

use Prophecy\Argument\Token;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Argument tokens shortcuts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Argument
{
    /**
     * Checks that argument is exact value or object.
     *
     * @param mixed $value
     *
     * @return Token\ExactValueToken
     */
    public static function exact($value)
    {
        return new Token\ExactValueToken($value);
    }

    /**
     * Checks that argument is of specific type or instance of specific class.
     *
     * @param string $type Type name (`integer`, `string`) or full class name
     *
     * @return Token\TypeToken
     */
    public static function type($type)
    {
        return new Token\TypeToken($type);
    }

    /**
     * Checks that argument object has specific state.
     *
     * @param string $methodName
     * @param mixed  $value
     *
     * @return Token\ObjectStateToken
     */
    public static function which($methodName, $value)
    {
        return new Token\ObjectStateToken($methodName, $value);
    }

    /**
     * Checks that argument matches provided callback.
     *
     * @param callable $callback
     *
     * @return Token\CallbackToken
     */
    public static function that($callback)
    {
        return new Token\CallbackToken($callback);
    }

    /**
     * Matches any single value.
     *
     * @return Token\AnyValueToken
     */
    public static function any()
    {
        return new Token\AnyValueToken;
    }

    /**
     * Matches all values to the rest of the signature.
     *
     * @return Token\AnyValuesToken
     */
    public static function cetera()
    {
        return new Token\AnyValuesToken;
    }
}
