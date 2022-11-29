<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Doubler\Generator\Node\Type;
use RecursiveIterator;

/**
 * @see \ReflectionNamedType
 */
class NamedTypeNode extends TypeNodeAbstract implements Type
{
    protected $name;
    protected $isBuiltIn;

    public function __construct(string $name, bool $allowsNull = false, bool $isBuiltIn = false)
    {
        $this->name       = $name;
        $this->isBuiltIn  = $isBuiltIn;
        $this->allowsNull = $allowsNull;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isBuiltIn(): bool
    {
        return $this->isBuiltIn;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}