<?php

namespace Fixtures\Prophecy;

class NeverType
{
    public function doSomething(): never
    {
        throw new \RuntimeException();
    }
}
