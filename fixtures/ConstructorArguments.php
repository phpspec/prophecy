<?php

namespace Fixtures\Prophecy;

class ConstructorArguments
{
    public $arg_1;
    public $arg_2;
    public $arg_3;

    public function __construct(\ArrayAccess $arg_1, array $arg_2 = [], \ArrayAccess $arg_3 = null)
    {
        $this->arg_1 = $arg_1;
        $this->arg_2 = $arg_2;
        $this->arg_3 = $arg_3;
    }
}
