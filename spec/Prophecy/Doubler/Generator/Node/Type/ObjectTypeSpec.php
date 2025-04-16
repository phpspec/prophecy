<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use stdClass;

class ObjectTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(stdClass::class);
    }

    function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_is_stringable(): void
    {
        $this->beConstructedWith('stdClass');
        $this->getType()->shouldReturn('stdClass');
        $this->__toString()->shouldReturn('\stdClass');
    }
}
