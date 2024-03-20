<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

abstract class TypeNodeAbstract
{
    /** @var array<string, string> */
    protected $types = [];

    public function __construct(string ...$types)
    {
        foreach ($types as $type) {
            $type = $this->getRealType($type);
            $this->types[$type] = $type;
        }

        $this->guardIsValidType();
    }

    public function canUseNullShorthand(): bool
    {
        return isset($this->types['null']) && count($this->types) === 2;
    }

    /**
     * @return list<string>
     */
    public function getTypes(): array
    {
        return array_values($this->types);
    }

    /**
     * @return list<string>
     */
    public function getNonNullTypes(): array
    {
        $nonNullTypes = $this->types;
        unset($nonNullTypes['null']);

        return array_values($nonNullTypes);
    }

    protected function prefixWithNsSeparator(string $type): string
    {
        return '\\'.ltrim($type, '\\');
    }

    protected function getRealType(string $type): string
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
            case 'true':
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

    /**
     * @return void
     */
    protected function guardIsValidType()
    {
        if (\PHP_VERSION_ID < 80200) {
            if ($this->types == ['null' => 'null']) {
                throw new DoubleException('Type cannot be standalone null');
            }

            if ($this->types == ['false' => 'false']) {
                throw new DoubleException('Type cannot be standalone false');
            }

            if ($this->types == ['false' => 'false', 'null' => 'null']) {
                throw new DoubleException('Type cannot be nullable false');
            }

            if ($this->types == ['true' => 'true']) {
                throw new DoubleException('Type cannot be standalone true');
            }

            if ($this->types == ['true' => 'true', 'null' => 'null']) {
                throw new DoubleException('Type cannot be nullable true');
            }
        }

        if (\PHP_VERSION_ID >= 80000 && isset($this->types['mixed']) && count($this->types) !== 1) {
            throw new DoubleException('mixed cannot be part of a union');
        }
    }
}
