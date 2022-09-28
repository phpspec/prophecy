<?php

namespace Prophecy\Doubler\Generator\Node\Type;

use Prophecy\Doubler\Generator\Node\Type;

abstract class TypeNodeAbstract implements Type
{
    /** @var bool $allowsNull */
    protected $allowsNull;

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function canUseNullShorthand(): bool
    {
        return $this->allowsNull;
    }

    public function getTypes(): array
    {
        return [];
    }

    public function getNonNullTypes(): array
    {
    }
}