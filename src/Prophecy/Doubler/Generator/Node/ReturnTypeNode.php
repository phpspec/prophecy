<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function guardIsValidType()
    {
        if (in_array('void', $this->types) && count($this->types) !== 1) {
            throw new DoubleException('void cannot be part of a union');
        }
        if (in_array('never', $this->types) && count($this->types) !== 1) {
            throw new DoubleException('never cannot be part of a union');
        }

        parent::guardIsValidType();
    }

    /**
     * @deprecated use hasReturnStatement
     */
    public function isVoid()
    {
        return $this->types == ['void'];
    }

    public function hasReturnStatement(): bool
    {
        return $this->types !== ['void']
            && $this->types !== ['never'];
    }
}
