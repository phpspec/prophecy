<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;

class SimpleTypeSpec extends ObjectBehavior
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

    function it_prefix_namespace_with_antislash(): void
    {
        $this->beConstructedWith('Prophecy\\Doubler\\Generator\\Node\\Type\\SimpleType');
        $this->getType()->shouldReturn('\\Prophecy\\Doubler\\Generator\\Node\\Type\\SimpleType');
        $this->isBuiltin()->shouldReturn(false);
    }

    function it_resolves_builtin_aliases(): void
    {
        $this->beConstructedWith('double');
        $this->getType()->shouldReturn('float');
        $this->isBuiltin()->shouldReturn(true);
    }
}
