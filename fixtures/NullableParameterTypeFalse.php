<?php

namespace Fixtures\Prophecy;

class NullableParameterTypeFalse
{
    public function method(?false $arg)
    {
        return $arg;
    }
}
