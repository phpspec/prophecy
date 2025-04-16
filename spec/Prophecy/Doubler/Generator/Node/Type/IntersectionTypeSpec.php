<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

class IntersectionTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            new SimpleType('Foo'),
            new SimpleType('Bar'),
        ]);
    }

    function it_should_implement_type_union(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_should_throw_double_exception_for_builtin_types()
    {
        $this->beConstructedWith([
            new SimpleType('string'),
            new SimpleType('Foo'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_should_throw_double_exception_if_less_than_2_types_provided()
    {
        $this->beConstructedWith([
            new SimpleType('Bar'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_should_throw_double_exception_if_union_type_given(): void
    {
        $this->beConstructedWith([
            new SimpleType('Bar'),
            new UnionType([new SimpleType('Foo'), new SimpleType('Baz')]),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }
}
