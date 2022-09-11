<?php

namespace Fixtures\Prophecy;

class StandaloneParameterTypeTrue
{
    public function method(true $arg)
    {
        return $arg;
    }
}
