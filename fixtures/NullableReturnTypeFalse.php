<?php

namespace Fixtures\Prophecy;

class NullableReturnTypeFalse
{
    public function method(): ?false
    {
        return false;
    }
}
