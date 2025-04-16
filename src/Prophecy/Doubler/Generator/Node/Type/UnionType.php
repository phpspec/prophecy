<?php

namespace Prophecy\Doubler\Generator\Node\Type;

class UnionType extends AbstractType
{
    /**
     * @param list<AbstractType> $types
     */
    public function __construct(private array $types)
    {
        $this->guard();
    }

    /**
     * @return list<AbstractType>
     */
    public function getTypes(): array
    {
        return $this->types;
    }
    public function isRecursive(): bool
    {
        foreach ($this->types as $type) {
            if ($type instanceof IntersectionType) {
                return true;
            }
        }

        return false;
    }

    private function guard(): void
    {
        // Cannot contain void or never
    }
}
