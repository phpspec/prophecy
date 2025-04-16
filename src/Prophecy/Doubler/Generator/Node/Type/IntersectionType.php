<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Exception\Doubler\DoubleException;

class IntersectionType implements TypeInterface
{
    /**
     * @param list<SimpleType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }

    /**
     * @return list<TypeInterface>
     */
    public function getTypes(): array
    {
        return $this->types;
    }

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
    }

    private function guard(): void
    {
        // Cannot contain void, never, null, scalar types, mixed, union types etc.
        foreach ($this->types as $type) {
            if (!$type instanceof SimpleType || $type->isBuiltin()) {
                throw new DoubleException('Intersection types can only contain class/interface names.');
            }
        }
        if (count($this->types) < 2) {
            throw new DoubleException('Intersection types must contain at least two types.');
        }
    }
}
