<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Exception\Doubler\DoubleException;

final class UnionType implements TypeInterface
{
    /**
     * @param list<SimpleType|IntersectionType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }

    /**
     * @return list<SimpleType|IntersectionType>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    private function guard(): void
    {
        $typeCount = count($this->types);

        if ($typeCount < 2) {
            // Throwing LogicException as this indicates misuse of the UnionType class itself.
            throw new DoubleException(sprintf(
                'UnionType must be constructed with at least two types. Got %d.',
                $typeCount
            ));
        }

        // To detect duplicates
        $typeStrings = [];

        foreach ($this->types as $type) {
            if ($type instanceof UnionType) {
                throw new DoubleException('Union types cannot contain other unions.');
            }
            if ($type instanceof IntersectionType) {
                $typeStrings[] = implode('&', array_map(fn(SimpleType $type) => (string) $type, $type->getTypes()));
                continue; // Valid type, nothing to be checked
            }
            if (!$type instanceof SimpleType) {
                throw new DoubleException(sprintf('Unexpected type "%s". Only IntersectionType and SimpleType are supported in UnionType.', get_class($type)));
            }
            $typeName = $type->getType();
            $typeStrings[] = $typeName;

            if (in_array($typeName, ['void', 'never', 'mixed'], true)) {
                throw new DoubleException(sprintf('Type "%s" cannot be part of a union type.', $typeName));
            }
        }

        // Rule: Union types cannot contain duplicate types (e.g., int|string|int is invalid).
        // Reflection usually resolves this, but it's good practice to ensure consistency.
        if (count(array_unique($typeStrings)) !== $typeCount) {
            throw new DoubleException(sprintf(
                'Union types cannot contain duplicate types. Found duplicates in: %s',
                implode('|', $typeStrings)
            ));
        }
    }

    public function has(SimpleType|IntersectionType $givenType): bool
    {
        foreach ($this->types as $type) {
            if ($type->equals($givenType)) {
                return true;
            }
        }

        return false;
    }

    public function equals(TypeInterface $givenType): bool
    {
        if (!$givenType instanceof UnionType) {
            return false;
        }

        if (count($this->types) !== count($givenType->getTypes())) {
            return false;
        }

        foreach ($this->types as $type) {
            if (!$givenType->has($type)) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        $result = '';
        foreach ($this->types as $type) {
            if ($result !== '') {
                $result .= '|';
            }

            if ($type instanceof IntersectionType && count($this->types) > 1) {
                $result .= '('.((string) $type).')';
                continue;
            }
            $result .= (string) $type;
        }

        return $result;
    }
}
