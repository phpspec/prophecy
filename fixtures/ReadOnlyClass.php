<?php

namespace Fixtures\Prophecy;

readonly class ReadOnlyClass
{
    public int $foo;

    public function __construct()
    {
        $this->foo = 1;
    }
}
