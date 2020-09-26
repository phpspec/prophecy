<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function getRealType(string $type): string
    {
        if ($type == 'void') {
            return $type;
        }

        return parent::getRealType($type);
    }

    protected function guardIsValidType()
    {
        if (isset($this->types['void']) && count($this->types) !== 1) {
            throw new DoubleException('void cannot be part of a union');
        }

        parent::guardIsValidType();
    }

    public function isVoid(): bool
    {
        return $this->types == ['void' => 'void'];
    }
}
