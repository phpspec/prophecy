<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

abstract class AbstractBaseClassWithMethodWithReturnType
{
    public function returnSelf(?\DateTimeInterface $test): self
    {
    }
}
