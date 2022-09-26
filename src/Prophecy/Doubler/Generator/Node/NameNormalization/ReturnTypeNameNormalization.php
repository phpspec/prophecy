<?php

namespace Prophecy\Doubler\Generator\Node\NameNormalization;

class ReturnTypeNameNormalization extends NameNormalizationAbstract
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
}