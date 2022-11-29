<?php

namespace Prophecy\Doubler\Generator\Node;

interface NameNormalization
{
    public function normalize(string ...$types): array;

    public function getRealType(string $type): string;
}