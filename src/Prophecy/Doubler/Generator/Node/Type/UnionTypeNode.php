<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Doubler\Generator\Node\ComplexType;
use Prophecy\Doubler\Generator\Node\Type;
use RecursiveIterator;

/**
 * @see \ReflectionUnionType
 */
class UnionTypeNode extends TypeNodeAbstract implements Type
{
    /**
     * @var NamedTypeNode[]
     */
    protected $types;

    /**
     * @param NamedTypeNode ...$types
     */
    public function __construct(bool $allowsNull = false, NamedTypeNode ...$types)
    {
        $this->allowsNull = $allowsNull;
        $this->types      = $types;
    }

    /**
     * @return NamedTypeNode[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function __toString(): string
    {
        $string = '';
        foreach ($this->types as $type) {
            $string .= $type . '|';
        }
        return rtrim($string, '|');
    }
}