<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;

class BuiltinTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('string');
    }

    function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_is_stringable(): void
    {
        $this->beConstructedWith('int');
        $this->getType()->shouldReturn('int');
        $this->__toString()->shouldReturn('int');
    }
}
