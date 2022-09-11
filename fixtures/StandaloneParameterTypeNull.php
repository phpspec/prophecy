<?php

namespace Fixtures\Prophecy;

class StandaloneParameterTypeNull
{
    public function method(null $arg)
    {
        return $arg;
    }
}
