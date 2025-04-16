<?php

namespace Prophecy\Doubler\Generator\Node\Type;

interface TypeInterface
{
    public function equals(TypeInterface $givenType): bool;
}
