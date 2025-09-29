<?php

namespace Prophecy\Doubler\Generator\Node\Type;

interface TypeInterface extends \Stringable
{
    public function equals(TypeInterface $givenType): bool;
}
