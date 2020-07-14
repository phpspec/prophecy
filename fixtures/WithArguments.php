<?php

namespace Fixtures\Prophecy;

class WithArguments
{
    public function methodWithArgs(\ArrayAccess $arg_1, array $arg_2 = [], \ArrayAccess $arg_3 = null)
    {
    }
    
    public function methodWithoutTypeHints($arg)
    {
    }
}
