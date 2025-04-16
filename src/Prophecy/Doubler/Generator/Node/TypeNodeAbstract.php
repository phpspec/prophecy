<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

abstract class TypeNodeAbstract
{
    protected TypeInterface|null $type;

    /**
     * @param string|TypeInterface ...$types
     */
    public function __construct(string|TypeInterface ...$types)
    {
        $deprecation = 'Only 1 type will be supported in the future, strings are no longer supported as type.';
        if (count($types) !== 1) {
            // TODO: trigger deprecation notice
        } else {
            foreach ($types as $type) {
                if (!$type instanceof TypeInterface) {
                    // TODO: deprecation notice
                    break;
                }
            }
        }

        // BC Layer for usage with strings
        $typesNormalized = [];
        $union = [];
        foreach ($types as $index => $type) {
            if (is_string($type)) {
                $type = new SimpleType($type);
                if (!in_array($type->getType(), $typesNormalized, true)) {
                    $union[] = $type;
                    $typesNormalized[] = $type->getType();
                }
                continue;
            }
            $union[] = $type;
        }

        // BC Layer for usage with many types
        if (count($union) > 1) {
            $this->type = new UnionType($union);
        } else {
            $this->type = $union[0] ?? null;
        }
    }

    public function canUseNullShorthand(): bool
    {
        if ($this->type instanceof UnionType) {
            return $this->type->has(new SimpleType('null')) && count($this->type->getTypes()) === 2;
        }

        return false;
    }

    /**
     * @return list<string>
     * @deprecated use getType() instead
     */
    public function getTypes(): array
    {
        // TODO: add deprecation notice
        if ($this->type instanceof SimpleType) {
            return [$this->type->getType()];
        }

        $types = [];

        if ($this->type instanceof UnionType) {
            foreach ($this->type->getTypes() as $type) {
                if ($type instanceof IntersectionType) {
                    throw new DoubleException('getType() method is deprecated and do not support IntersectionType by design. Use getType() instead.');
                }
                $types[$type->getType()] = $type->getType();
            }
        }

        return array_values($types);
    }

    public function getType(): TypeInterface
    {
        return $this->type;
    }

    /**
     * @deprecated use getType() instead
     * @return list<string>
     */
    public function getNonNullTypes(): array
    {
        if ($this->type === null) {
            return [];
        }
        if ($this->type instanceof UnionType) {
            $types = [];
            foreach ($this->type->getTypes() as $type) {
                if ($type->getType() === 'null') {
                    continue;
                }
                $types[] = $type->getType();
            }

            return $types;
        }

        if ($this->type instanceof SimpleType) {
            if ($this->type->getType() === 'null') {
                return [];
            }
            return [$this->type->getType()];
        }

        throw new DoubleException('getNonNullTypes() method is deprecated and do not support IntersectionType by design. Use getType() instead.');
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
            case 'mixed':
            case 'void':
            case 'never':
                return $type;
            default:
                // Class / Interface type
                return $this->prefixWithNsSeparator($type);
        }
    }

    /**
     * @todo: put this in SimpleType
     * @return void
     */
    protected function guardIsValidType()
    {
        if (\PHP_VERSION_ID < 80200) {
            if ($this->type->equals(new SimpleType('null'))) {
                throw new DoubleException('Type cannot be standalone null');
            }

            if ($this->type->equals(new SimpleType('false'))) {
                throw new DoubleException('Type cannot be standalone false');
            }

            if ($this->type->equals(new UnionType([new SimpleType('false'), new SimpleType('null')]))) {
                throw new DoubleException('Type cannot be nullable false');
            }

            if ($this->type->equals(new SimpleType('true'))) {
                throw new DoubleException('Type cannot be standalone true');
            }

            if ($this->type->equals(new UnionType([new SimpleType('true'), new SimpleType('null')]))) {
                throw new DoubleException('Type cannot be nullable true');
            }
        }
    }
}
