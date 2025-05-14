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
