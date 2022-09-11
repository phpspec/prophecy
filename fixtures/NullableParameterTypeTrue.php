<?php

namespace Fixtures\Prophecy;

class NullableParameterTypeTrue
{
    public function method(?true $arg)
    {
        return $arg;
    }
}
