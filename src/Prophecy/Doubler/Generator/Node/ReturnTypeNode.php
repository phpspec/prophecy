<?php

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Exception\Doubler\DoubleException;

final class ReturnTypeNode extends TypeNodeAbstract
{
    protected function isBuiltIn(string $type): bool
    {
        switch ($type) {
            case 'void':
            case 'never':
                return true;
            default:
                return parent::isBuiltIn($type);
        }
    }

    /**
     * @deprecated use hasReturnStatement
     *
     * @return bool
     */
    public function isVoid(): bool
    {
        if ($this->getType() === null) {
            return true;
        }

        return $this->getType()->equals(new BuiltinType('void'));
    }

    public function hasReturnStatement(): bool
    {
        if ($this->getType() === null) {
            return true;
        }

        return !$this->getType()->equals(new BuiltinType('void'))
            && !$this->getType()->equals(new BuiltinType('never'));
    }
}
