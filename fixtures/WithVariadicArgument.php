<?php

namespace Fixtures\Prophecy;

class WithVariadicArgument
{
    function methodWithArgs(...$args)
    {
    }

    function methodWithTypeHintedArgs(array ...$args)
    {
    }
}
