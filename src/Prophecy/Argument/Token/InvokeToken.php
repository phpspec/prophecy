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

use Prophecy\Util\StringUtil;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Invoke callback.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class InvokeToken implements TokenInterface
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var false
     */
    private $expectResult;

    /**
     * @var mixed
     */
    private $expectedResult;

    /**
     * @var StringUtil
     */
    private $util;

    /**
     * @param mixed[] $arguments
     * @param bool    $expectResult
     * @param mixed   $expectedResult
     */
    public function __construct(array $arguments, $expectResult = false, $expectedResult = null)
    {
        $this->arguments      = $arguments;
        $this->expectResult   = $expectResult;
        $this->expectedResult = $expectedResult;
        $this->util           = new StringUtil();
    }

    /**
     * Execute the callback with arguments and score 3 (same as AnyValueToken)
     * if successful.
     *
     * @param callable $callback
     *
     * @return int|bool
     */
    public function scoreArgument($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf(
                'Callable expected as an argument to CallbackToken, but got %s.',
                gettype($callback)
            ));
        }

        $result = call_user_func_array($callback, $this->arguments);

        $score = 3;

        if ($this->expectResult && $result != $this->expectedResult) {
            $score = false;
        }

        return $score;
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
        return sprintf(
            '%sinvoke(%s)',
            $this->expectResult ? $this->util->stringify($this->expectedResult) . ' == ' : '',
            $this->createArgumentString()
        );
    }

    /**
     * Returns string representation a list of values.
     *
     * @return string
     */
    private function createArgumentString()
    {
        return implode(', ', array_map(
            array($this->util, 'stringify'),
            $this->arguments
        ));
    }
}
