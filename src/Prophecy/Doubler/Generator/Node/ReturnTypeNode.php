<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function getRealType(string $type): string
    {
        switch ($type) {
            case 'void':
            case 'never':
                return $type;
            default:
                return parent::getRealType($type);
        }
    }

    protected function guardIsValidType()
    {
        if (isset($this->types['void']) && count($this->types) !== 1) {
            throw new DoubleException('void cannot be part of a union');
        }
        if (isset($this->types['never']) && count($this->types) !== 1) {
            throw new DoubleException('never cannot be part of a union');
        }

        parent::guardIsValidType();
    }

    /**
     * @deprecated use hasReturnStatement
     *
     * @return bool
     */
    public function isVoid(): bool
    {
        if ($this->type === null) {
            return true;
        }

        return $this->type->equals(new SimpleType('void'));
    }

    public function hasReturnStatement(): bool
    {
        if ($this->type === null) {
            return true;
        }

        return !$this->type->equals(new SimpleType('void'))
            && !$this->type->equals(new SimpleType('never'));
    }
}
