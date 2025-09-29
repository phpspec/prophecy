<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Exception\Doubler\DoubleException;

final class IntersectionType implements TypeInterface
{
    /**
     * @param list<ObjectType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }

    /**
     * @return list<ObjectType>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param SimpleType $givenType
     */
    private function has(SimpleType $givenType): bool
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
        if (!$givenType instanceof IntersectionType) {
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

    private function guard(): void
    {
        // Cannot contain void, never, null, scalar types, mixed, union types etc.
        foreach ($this->types as $type) {
            if (!$type instanceof ObjectType) {
                throw new DoubleException('Intersection types can only contain class/interface names.');
            }
        }
        if (count($this->types) < 2) {
            throw new DoubleException('Intersection types must contain at least two types.');
        }
    }

    public function __toString(): string
    {
        $result = '';
        foreach ($this->types as $type) {
            if ($result !== '') {
                $result .= '&';
            }
            $result .= (string) $type;
        }

        return $result;
    }
}
