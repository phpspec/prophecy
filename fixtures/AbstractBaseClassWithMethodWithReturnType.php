<?php

declare(strict_types=1);

namespace Fixtures\Prophecy;

abstract class AbstractBaseClassWithMethodWithReturnType implements EmptyInterface
{
    private $test;

    public function test(?\DateTimeInterface $test): self
    {
        $this->test = $test;

        return $this;
    }
}
