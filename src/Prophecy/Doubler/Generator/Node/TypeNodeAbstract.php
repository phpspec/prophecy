<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\IntersectionTypeNode;
use Prophecy\Doubler\Generator\Node\Type\NamedTypeNode;
use Prophecy\Doubler\Generator\Node\Type\UnionTypeNode;
use Prophecy\Exception\Doubler\DoubleException;

abstract class TypeNodeAbstract
{
    /** @var ?Type $type */
    protected $type;

    public function __construct(?Type $type = null)
    {
        $this->type = $type;
        $this->guardIsValidType();
    }

    public function canUseNullShorthand(): bool
    {
        return $this->type instanceof NamedTypeNode
            && $this->type->getName() !== 'mixed'
            && $this->type->allowsNull();
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    protected function guardIsValidType()
    {
        if ($this->type instanceof UnionTypeNode) {
            /** @var NamedTypeNode $type */
            foreach ($this->type->getTypes() as $type) {
                if (\PHP_VERSION_ID >= 80000 && $type->getName() === 'mixed') {
                    throw new DoubleException('mixed cannot be part of a union');
                }
            }
        }
        elseif($this->type instanceof IntersectionTypeNode)
        {
            /** @var NamedTypeNode $type */
            foreach ($this->type->getTypes() as $type) {
                if (\PHP_VERSION_ID >= 80000 && $type->getName() === 'mixed') {
                    throw new DoubleException('mixed cannot be part of an intersection');
                }
            }
        }
        elseif($this->type instanceof NamedTypeNode)
        {
            if ($this->type->getName() === 'null') {
                throw new DoubleException('Type cannot be standalone null');
            }

            if ($this->type->getName() === 'false' && $this->type->allowsNull()) {
                throw new DoubleException('Type cannot be nullable false');
            }

            if ($this->type->getName() === 'false') {
                throw new DoubleException('Type cannot be standalone false');
            }
        }
    }
}
