<?php

namespace Fixtures\Prophecy;

class WithFinalVirtuallyPrivateMethod
{
    final public function __toString()
    {
        return '';
    }

    final public function _getName()
    {
    }
}
