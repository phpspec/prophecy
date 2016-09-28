<?php

namespace Fixtures\Prophecy;

class WithArguments
{
    public function methodWithArgs(array $arg_1 = array(), \ArrayAccess $arg_2, \ArrayAccess $arg_3 = null)
    {
    }
    
    public function methodWithoutTypeHints($arg)
    {
    }
}
