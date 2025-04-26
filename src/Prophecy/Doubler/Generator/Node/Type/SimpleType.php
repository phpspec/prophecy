<?php

namespace Prophecy\Doubler\Generator\Node\Type;

class SimpleType implements TypeInterface, \Stringable
{
    private string $type;
    private bool $builtin;
    public function __construct(string $type)
    {
        $this->type = $this->normalizeType($type);
    }

    public function isBuiltin(): bool
    {
        return $this->builtin;
    }

    private function normalizeType(string $type): string
    {
        $this->builtin = true;
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
            case 'true':
            case 'float':
            case 'int':
            case 'string':
            case 'iterable':
            case 'object':
            case 'null':
            case 'mixed':
            case 'void':
            case 'never':
                return $type;
            // Class / Interface type
            default:
                $this->builtin = false;
                return $this->prefixWithNsSeparator($type);
        }
    }

    private function prefixWithNsSeparator(string $type): string
    {
        // Avoid double-prefixing if already prefixed
        if (str_starts_with($type, '\\')) {
            return $type;
        }
        return '\\'. $type;
    }

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function equals(TypeInterface $givenType): bool
    {
        if (!$givenType instanceof SimpleType) {
            return false;
        }

        return $this->type === $givenType->getType();
    }
}
