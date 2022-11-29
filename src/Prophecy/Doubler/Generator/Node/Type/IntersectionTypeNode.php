<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Doubler\Generator\Node\ComplexType;
use Prophecy\Doubler\Generator\Node\Type;
use RecursiveIterator;

/**
 * @see \ReflectionIntersectionType
 */
class IntersectionTypeNode extends TypeNodeAbstract implements Type
{
    /**
     * @var NamedTypeNode[]
     */
    protected $types;

    /**
     * @param NamedTypeNode ...$types
     */
    public function __construct(bool $allowsNulls, NamedTypeNode ...$types)
    {
        $this->allowsNull = $allowsNulls;
        $this->types      = $types;
    }

    /**
     * @return NamedTypeNode[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function hasReturnStatement(): bool
    {
        return true;
    }

    public function __toString(): string
    {
        $string = '';
        foreach ($this->types as $type) {
            $string .= $type . '&';
        }
        return '(' . rtrim($string, '&') . ')';
    }
}