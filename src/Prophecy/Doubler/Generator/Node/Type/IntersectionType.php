<?php

namespace Prophecy\Doubler\Generator\Node\Type;

class IntersectionType extends AbstractType
{
    private function guard(): void
    {
        // Cannot contain void, never or union
    }
}
