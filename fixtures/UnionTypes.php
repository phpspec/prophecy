<?php

namespace Fixtures\Prophecy;

class UnionTypes
{
    public function doSomething(int|string $arg): bool|stdClass
    {

    }
}
