<?php

namespace Fixtures\Prophecy;

interface SelfReferencing
{
    public function __invoke(self $self): self;
}
