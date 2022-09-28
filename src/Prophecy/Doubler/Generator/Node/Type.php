<?php

namespace Prophecy\Doubler\Generator\Node;

interface Type
{
    public function allowsNull(): bool;
}