<?php

namespace Fixtures\Prophecy;

class TentativeReturnTypeNull
{
    #[\ReturnTypeWillChange()]
    public function method(): ?string
    {
        return null;
    }
}
