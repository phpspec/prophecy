<?php

namespace Prophecy\Util;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * String utility.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StringUtil
{
    /**
     * Stringifies any provided value.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function stringify($value)
    {
        if (is_array($value)) {
            return '['.implode(', ', array_map(array($this, __FUNCTION__), $value)).']';
        }
        if (is_resource($value)) {
            return get_resource_type($value).':'.$value;
        }
        if (is_object($value)) {
            return sprintf('%s:%s', get_class($value), spl_object_hash($value));
        }
        if (true === $value || false === $value) {
            return $value ? 'true' : 'false';
        }
        if (is_string($value)) {
            return sprintf('"%s"', str_replace("\n", '\\n', $value));
        }
        if (null === $value) {
            return 'null';
        }

        return (string) $value;
    }
}
