<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\ObjectType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

abstract class TypeNodeAbstract
{
    // null means no type, NOT BuiltInType("null")
    private ?TypeInterface $type;

    /**
     * @param string|TypeInterface|null $type
     */
    public function __construct(string|TypeInterface|null $type = null, string ...$types)
    {
        if (!empty($types) || is_string($type)) {
            $types = [$type, ...$types];
        }

        if (!empty($types)) {
            // BC Layer for usage with strings
            trigger_deprecation(
                'phpspec/prophecy',
                '1.23',
                'Instanciating node type with a string type will not be supported in the future, use a TypeInterface instance instead.',
            );

            // BC Layer for usage with strings
            $typesNormalized = [];
            /** @var list<BuiltinType|ObjectType|IntersectionType> $union */
            $union = [];
            foreach ($types as $type) {
                if (!is_string($type)) {
                    throw new DoubleException('Building a TypeNode with string is deprecated. Mixing strings and type object is not allowed.');
                }

                if ($this->isBuiltIn($type)) {
                    $type = new BuiltinType($this->normalizeBuiltinType($type));
                } else {
                    /** @var class-string $typeName */
                    $typeName = $this->removePrefixNsSeparator($type);
                    $type = new ObjectType($typeName);
                }
                if (!in_array($type->getType(), $typesNormalized, true)) {
                    $union[] = $type;
                    $typesNormalized[] = $type->getType();
                }
            }

            if (count($union) > 1) {
                $this->type = new UnionType($union);
            } else {
                $this->type = $union[0];
            }
        } else {
            /** @var TypeInterface|null $type */
            $this->type = $type;
        }
    }

    /**
     * @deprecated use isNullable() instead
     */
    public function canUseNullShorthand(): bool
    {
        trigger_deprecation(
            'phpspec/prophecy',
            '1.23',
            'This method is deprecated in favor of nullable()'
        );
        if ($this->type instanceof UnionType) {
            return $this->type->has(new BuiltinType('null')) && count($this->type->getTypes()) === 2;
        }

        return false;
    }

    public function isNullable(): bool
    {
        if ($this->type instanceof UnionType) {
            return $this->type->has(new BuiltinType('null'));
        }

        if ($this->type instanceof SimpleType && $this->type->getType() === 'null') {
            return true;
        }

        return false;
    }

    /**
     * @return list<string>
     * @deprecated use getType() instead
     */
    public function getTypes(): array
    {
        trigger_deprecation(
            'phpspec/prophecy',
            '1.23',
            'This method is deprecated in favor of getType()',
        );
        if ($this->type instanceof SimpleType) {
            return [(string) $this->type];
        }

        $types = [];

        if ($this->type instanceof UnionType) {
            foreach ($this->type->getTypes() as $type) {
                if ($type instanceof IntersectionType) {
                    throw new DoubleException('getType() method is deprecated and do not support IntersectionType by design. Use getType() instead.');
                }
                $types[$type->getType()] = (string) $type;
            }
        }

        $types =  array_values($types);
        $types = array_map([$this, 'normalizeBuiltinType'], $types);

        return array_values(array_unique($types));
    }

    public function getType(): ?TypeInterface
    {
        return $this->type;
    }

    /**
     * @deprecated use getType() instead
     * @return list<string>
     */
    public function getNonNullTypes(): array
    {
        trigger_deprecation(
            'phpspec/prophecy',
            '1.23',
            'This method is deprecated in favor of getType() and the usage of the new type API.',
        );
        if ($this->type === null) {
            return [];
        }
        if ($this->type instanceof UnionType) {
            $types = [];
            foreach ($this->type->getTypes() as $type) {
                if ($type instanceof IntersectionType) {
                    throw new DoubleException('You are using the old (and deprecated) API which is not compatible with intersections');
                }
                if (!$type instanceof BuiltinType || $type->getType() !== 'null') {
                    $types[] = $type->getType();
                }
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

    private function normalizeBuiltinType(string $type): string
    {
        switch ($type) {
            // normalize alias types
            case 'double':
            case 'real':
                return 'float';
            case 'integer':
                return 'int';
            case 'boolean':
                return 'bool';
            default:
                return $type;
        }
    }

    protected function isBuiltIn(string $type): bool
    {
        switch ($type) {
            // type aliases
            case 'double':
            case 'real':
            case 'boolean':
            case 'integer':

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
                return true;
            default:
                // Class / Interface type
                return false;
        }
    }

    protected function removePrefixNsSeparator(string $type): string
    {
        return ltrim($type, '\\');
    }
}
