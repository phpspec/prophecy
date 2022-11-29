<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\IntersectionTypeNode;
use Prophecy\Doubler\Generator\Node\Type\NamedTypeNode;
use Prophecy\Doubler\Generator\Node\Type\UnionTypeNode;
use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function guardIsValidType()
    {
        if ($this->type instanceof UnionTypeNode) {
            /** @var NamedTypeNode $type */
            foreach ($this->type->getTypes() as $type) {
                if ($type->getName() === 'void') {
                    throw new DoubleException('void cannot be part of a union');
                }
                elseif ($type->getName() === 'never')
                {
                    throw new DoubleException('never cannot be part of a union');
                }
            }
        }

        parent::guardIsValidType();
    }

    /**
     * @deprecated use hasReturnStatement
     */
    public function isVoid()
    {
        return $this->type instanceof NamedTypeNode
            && $this->type->getName() == 'void';
    }

    public function hasReturnStatement(): bool
    {
        if (!$this->type instanceof Type) {
            return false;
        }

       if ($this->type instanceof NamedTypeNode
           && in_array($this->type->getName(), ['void', 'never']))
       {
           return false;
       }

       return true;
    }
}
