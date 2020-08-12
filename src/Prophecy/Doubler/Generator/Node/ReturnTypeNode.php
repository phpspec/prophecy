<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode
{
    /** @var string[] */
    private $types = [];

    public function __construct(string ...$types)
    {
        foreach($types as $type) {
            $type = $this->getRealType($type);
            $this->types[$type] = $type;
        }

        $this->guardIsValidReturn();
    }

    private function getRealType(string $type): string
    {
        switch($type) {
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
            case 'array':
            case 'callable':
            case 'bool':
            case 'float':
            case 'int':
            case 'string':
            case 'iterable':
            case 'object':
            case 'void':
            case 'null':
                return $type;
            case 'mixed':
                return PHP_VERSION_ID < 80000 ? $this->prefixWithNsSeparator($type) : $type;

            default:
                return $this->prefixWithNsSeparator($type);
        }
    }

    private function guardIsValidReturn()
    {
        if ($this->types == ['null'=>'null']) {
            throw new DoubleException('Return type cannot be standalone null');
        }

        if (isset($this->types['void']) && count($this->types) != 1) {
            throw new DoubleException('void cannot be part of a union');
        }

        if (PHP_VERSION_ID >= 80000 && isset($this->types['mixed']) && count($this->types) != 1) {
            throw new DoubleException('mixed cannot be part of a union');
        }
    }

    public function getTypes() : array
    {
        return array_values($this->types);
    }

    public function canUseNullShorthand(): bool
    {
        return isset($this->types['null']) && count($this->types) <= 2;
    }

    public function getNonNullTypes(): array
    {
        return array_values(array_filter(
            $this->types,
            function(string $type) {
                return $type != 'null';
            }
        ));
    }

    private function prefixWithNsSeparator(string $type): string
    {
        return '\\' . ltrim('\\' . $type, '\\');
    }

    public function isVoid(): bool
    {
        return $this->types == ['void' => 'void'];
    }
}
