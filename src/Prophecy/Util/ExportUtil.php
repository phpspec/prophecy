<?php

namespace Prophecy\Util;

use Prophecy\Prophecy\ProphecyInterface;
use SebastianBergmann\RecursionContext\Context;

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class is a modification from sebastianbergmann/exporter
 * @see https://github.com/sebastianbergmann/exporter
 */
class ExportUtil
{
    /**
     * Exports a value as a string
     *
     * The output of this method is similar to the output of print_r(), but
     * improved in various aspects:
     *
     *  - NULL is rendered as "null" (instead of "")
     *  - TRUE is rendered as "true" (instead of "1")
     *  - FALSE is rendered as "false" (instead of "")
     *  - Strings are always quoted with single quotes
     *  - Carriage returns and newlines are normalized to \n
     *  - Recursion and repeated rendering is treated properly
     *
     * @param  mixed  $value
     * @param  int    $indentation The indentation level of the 2nd+ line
     * @return string
     */
    public static function export($value, $indentation = 0)
    {
        return self::recursiveExport($value, $indentation);
    }

    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @param  mixed $value
     * @return array<mixed>
     */
    public static function toArray($value)
    {
        if (!is_object($value)) {
            return (array) $value;
        }

        $array = array();

        foreach ((array) $value as $key => $val) {
            // properties are transformed to keys in the following way:
            // private   $property => "\0Classname\0property"
            // protected $property => "\0*\0property"
            // public    $property => "property"
            if (preg_match('/^\0.+\0(.+)$/', $key, $matches)) {
                $key = $matches[1];
            }

            // See https://github.com/php/php-src/commit/5721132
            if ($key === "\0gcdata") {
                continue;
            }

            $array[$key] = $val;
        }

        // Some internal classes like SplObjectStorage don't work with the
        // above (fast) mechanism nor with reflection in Zend.
        // Format the output similarly to print_r() in this case
        if ($value instanceof \SplObjectStorage) {
            foreach ($value as $key => $val) {
                // Use the same identifier that would be printed alongside the object's representation elsewhere.
                $array[spl_object_id($val)] = array(
                    'obj' => $val,
                    'inf' => $value->getInfo(),
                );
            }
        }

        return $array;
    }

    /**
     * Recursive implementation of export
     *
     * @param  mixed                                       $value       The value to export
     * @param  int                                         $indentation The indentation level of the 2nd+ line
     * @param  \SebastianBergmann\RecursionContext\Context $processed   Previously processed objects
     * @return string
     * @see    SebastianBergmann\Exporter\Exporter::export
     */
    protected static function recursiveExport(&$value, $indentation, $processed = null)
    {
        if ($value === null) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_float($value) && floatval(intval($value)) === $value) {
            return "$value.0";
        }

        if (is_resource($value)) {
            return sprintf(
                'resource(%d) of type (%s)',
                (int) $value,
                get_resource_type($value)
            );
        }

        if (is_string($value)) {
            // Match for most non printable chars somewhat taking multibyte chars into account
            if (preg_match('/[^\x09-\x0d\x20-\xff]/', $value)) {
                return 'Binary String: 0x' . bin2hex($value);
            }

            return "'" .
            str_replace(array("\r\n", "\n\r", "\r"), array("\n", "\n", "\n"), $value) .
            "'";
        }

        $whitespace = str_repeat(' ', 4 * $indentation);

        if (!$processed) {
            $processed = new Context;
        }

        if (is_array($value)) {
            if (($key = $processed->contains($value)) !== false) {
                return 'Array &' . $key;
            }

            \assert(\is_array($value));
            $array  = $value;
            $key    = $processed->add($value);
            $values = '';

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        self::recursiveExport($k, $indentation),
                        self::recursiveExport($value[$k], $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('Array &%s (%s)', $key, $values);
        }

        if (is_object($value)) {
            $class = get_class($value);

            if ($processed->contains($value)) {
                \assert(\is_object($value));
                return sprintf('%s#%d Object', $class, spl_object_id($value));
            }

            $processed->add($value);
            \assert(\is_object($value));
            $values = '';
            $array  = self::toArray($value);

            if (count($array) > 0) {
                foreach ($array as $k => $v) {
                    $values .= sprintf(
                        '%s    %s => %s' . "\n",
                        $whitespace,
                        self::recursiveExport($k, $indentation),
                        self::recursiveExport($v, $indentation + 1, $processed)
                    );
                }

                $values = "\n" . $values . $whitespace;
            }

            return sprintf('%s#%d Object (%s)', $class, spl_object_id($value), $values);
        }

        return var_export($value, true);
    }
}
