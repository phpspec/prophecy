<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\ObjectType;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

class IntersectionTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            new ObjectType('Foo'),
            new ObjectType('Bar'),
        ]);
    }

    function it_should_implement_type_union(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_should_throw_double_exception_for_builtin_types()
    {
        $this->beConstructedWith([
            new BuiltinType('string'),
            new ObjectType('Foo'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_should_throw_double_exception_if_less_than_2_types_provided()
    {
        $this->beConstructedWith([
            new ObjectType('Bar'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_should_throw_double_exception_if_union_type_given(): void
    {
        $this->beConstructedWith([
            new ObjectType('Bar'),
            new UnionType([new ObjectType('Foo'), new ObjectType('Baz')]),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_is_stringable(): void
    {
        $bar = new ObjectType('Bar');
        $foo = new ObjectType('Foo');
        $this->beConstructedWith([
            $bar,
            $foo,
        ]);
        $this->__toString()->shouldReturn('\\Bar&\\Foo');
    }
}
