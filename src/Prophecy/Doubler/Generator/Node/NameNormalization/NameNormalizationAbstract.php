<?php

namespace Prophecy\Doubler\Generator\Node\NameNormalization;

use Prophecy\Doubler\Generator\Node\NameNormalization;

abstract class NameNormalizationAbstract implements NameNormalization
{
    public function normalize(string ...$types): array
    {
        $normalizedTypes = [];
        foreach ($types as $type) {
            $type = $this->getRealType($type);
            $normalizedTypes[$type] = $type;
        }

        return array_values($normalizedTypes);
    }

    public function getRealType(string $type): string
    {
        switch ($type) {
            // type aliases
            case 'double':
            case 'real':
                return 'float';
            case 'boolean':
                return 'bool';
            case 'integer':
                return 'int';

            //  built in types
            case 'self':
            case 'static':
            case 'array':
            case 'callable':
            case 'bool':
            case 'false':
            case 'float':
            case 'int':
            case 'string':
            case 'iterable':
            case 'object':
            case 'null':
                return $type;
            case 'mixed':
                return \PHP_VERSION_ID < 80000 ? $this->prefixWithNsSeparator($type) : $type;

            default:
                return $this->prefixWithNsSeparator($type);
        }
    }

    public function prefixWithNsSeparator(string $type): string
    {
        return '\\' . ltrim($type, '\\');
    }
}