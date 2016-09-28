<?php

namespace Fixtures\Prophecy;

class WithReturnTypehints extends EmptyClass
{
    public function getSelf(): self {
        return $this;
    }

    public function getName(): string {
        return __CLASS__;
    }
    
    public function getParent(): parent {
        return $this;
    }
}
