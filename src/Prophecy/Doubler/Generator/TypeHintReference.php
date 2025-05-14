<?php

namespace Prophecy\Doubler\Generator;

/**
 * Tells whether a keyword refers to a class or to a built-in type for the
 * current version of php
 *
 * @deprecated in favour of Node\TypeNodeAbstract
 */
final class TypeHintReference
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function isBuiltInParamTypeHint($type)
    {
        switch ($type) {
            case 'self':
            case 'array':
            case 'callable':
            case 'bool':
            case 'float':
            case 'int':
            case 'string':
            case 'iterable':
            case 'object':
            case 'mixed':
                return true;

            default:
                return false;
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isBuiltInReturnTypeHint($type)
    {
        if ($type === 'void') {
            return true;
        }

        return $this->isBuiltInParamTypeHint($type);
    }
}
