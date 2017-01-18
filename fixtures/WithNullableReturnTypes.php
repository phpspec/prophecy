<?php

namespace Fixtures\Prophecy;

class WithNullableReturnTypes extends EmptyClass
{
    public function getSelf(): ?self {
        return null;
    }

    public function getName(): ?string {
        return null;
    }

    public function getParent(): ?parent {
        return null;
    }
}
