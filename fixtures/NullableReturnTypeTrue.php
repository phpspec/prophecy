<?php

namespace Fixtures\Prophecy;

class NullableReturnTypeTrue
{
    public function method(): ?true
    {
        return true;
    }
}
