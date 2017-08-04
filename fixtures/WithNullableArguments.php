<?php

namespace Fixtures\Prophecy;

class WithNullableArguments
{
    public function methodWithArgs(bool $arg_1 = true, ?bool $arg_2, ?bool $arg_3 = true, ?bool $arg_4 = null)
    {
    }
}
