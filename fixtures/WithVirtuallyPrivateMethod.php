<?php

namespace Fixtures\Prophecy;

class WithVirtuallyPrivateMethod
{
    public function __toString()
    {
        return '';
    }

    public function _getName()
    {
    }

    public function isAbstract()
    {
    }
}
