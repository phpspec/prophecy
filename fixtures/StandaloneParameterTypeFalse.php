<?php

namespace Fixtures\Prophecy;

class StandaloneParameterTypeFalse
{
    public function method(false $arg)
    {
        return $arg;
    }
}
