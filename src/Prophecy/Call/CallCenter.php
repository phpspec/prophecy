<?php

namespace Prophecy\Call;

use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Argument\ArgumentsWildcard;
use Prophecy\Util\StringUtil;

use Prophecy\Exception\Call\UnexpectedCallException;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Calls receiver & manager.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallCenter
{
    private $util;
    private $recordedCalls = array();

    /**
     * Initializes call center.
     *
     * @param StringUtil $util
     */
    public function __construct(StringUtil $util = null)
    {
        $this->util = $util ?: new StringUtil;
    }

    /**
     * Makes and records specific method call for object prophecy.
     *
     * @param ObjectProphecy $prophecy
     * @param string         $methodName
     * @param array          $arguments
     *
     * @return null|mixed  Returns null if no promise for prophecy found or
     *                     promise return value.
     *
     * @throws \Prophecy\Exception\Call\UnexpectedCallException  If no appropriate method prophecy found
     */
    public function makeCall(ObjectProphecy $prophecy, $methodName, array $arguments)
    {
        $backtrace = debug_backtrace();

        $file = $line = null;
        if (isset($backtrace[2]) && isset($backtrace[2]['file'])) {
            $file = $backtrace[2]['file'];
            $line = $backtrace[2]['line'];
        }

        // If no method prophecies defined, then it's a dummy, so we'll just return null
        if (0 == count($prophecy->getMethodProphecies())) {
            $this->recordedCalls[] = new Call($methodName, $arguments, null, $file, $line);

            return null;
        }

        // There are method prophecies, so it's a fake/stub. Searching prophecy for this call
        $matches = array();
        foreach ($prophecy->getMethodProphecies($methodName) as $methodProphecy) {
            if (0 < $score = $methodProphecy->getArgumentsWildcard()->scoreArguments($arguments)) {
                $matches[] = array($score, $methodProphecy);
            }
        }

        // If fake/stub doesn't have method prophecy for this call - throw exception
        if (!count($matches)) {
            throw $this->createUnexpectedCallException($prophecy, $methodName, $arguments);
        }

        // Sort matches by their score value
        @usort($matches, function($match1, $match2) { return $match2[0] - $match1[0]; });

        // If Highest rated method prophecy has a promise - execute it or return null instead
        $returnValue = null;
        if ($promise = $matches[0][1]->getPromise()) {
            $returnValue = $promise->execute($arguments, $prophecy, $matches[0][1]);
        }

        $this->recordedCalls[] = new Call($methodName, $arguments, $returnValue, $file, $line);

        return $returnValue;
    }

    /**
     * Searches for calls by method name & arguments wildcard.
     *
     * @param string            $methodName
     * @param ArgumentsWildcard $wildcard
     *
     * @return array
     */
    public function findCalls($methodName, ArgumentsWildcard $wildcard)
    {
        return array_values(
            array_filter($this->recordedCalls, function($call) use($methodName, $wildcard) {
                return $methodName === $call->getMethodName()
                    && 0 < $wildcard->scoreArguments($call->getArguments())
                ;
            })
        );
    }

    private function createUnexpectedCallException(ObjectProphecy $prophecy, $methodName,
                                                   array $arguments)
    {
        $classname = get_class($prophecy->reveal());
        $argstring = implode(', ', array_map(array($this->util, 'stringify'), $arguments));
        $expected  = implode("\n", array_map(function($methodProphecy) {
            return sprintf(' - %s(%s)',
                $methodProphecy->getMethodName(),
                $methodProphecy->getArgumentsWildcard()
            );
        }, $prophecy->getMethodProphecies($methodName)));

        return new UnexpectedCallException(
            sprintf("Method call `%s->%s(%s)` was not expected. Expected calls are:\n%s",
                $classname, $methodName, $argstring, $expected
            ),
            $prophecy, $methodName, $arguments
        );
    }
}
