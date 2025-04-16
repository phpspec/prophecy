<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\BuiltinType;
use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\ObjectType;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

class UnionTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            new BuiltinType('int'),
            new BuiltinType('string'),
        ]);
    }
    function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_throws_double_exception_when_union_type_given(): void
    {
        $this->beConstructedWith([
            new UnionType([new BuiltinType('int'), new BuiltinType('string')]),
            new BuiltinType('bool'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_types_duplicated(): void
    {
        $this->beConstructedWith([new BuiltinType('string'), new BuiltinType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_void(): void
    {
        $this->beConstructedWith([new BuiltinType('void'), new BuiltinType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_never(): void
    {
        $this->beConstructedWith([new BuiltinType('never'), new BuiltinType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_mixed(): void
    {
        $this->beConstructedWith([new BuiltinType('mixed'), new BuiltinType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_only_one_type(): void
    {
        $this->beConstructedWith([new BuiltinType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_return_array_of_its_types(): void
    {
        $this->getTypes()->shouldBeLike([
            new BuiltinType('int'),
            new BuiltinType('string'),
        ]);
    }

    function it_should_accept_simple_type_and_intersection()
    {
        $type1 = new BuiltinType('string');
        $type2 = new IntersectionType([new ObjectType('A'), new ObjectType('B')]);
        $this->beConstructedWith([$type1, $type2]);

        $this->has($type1)->shouldBe(true);
        $this->has($type2)->shouldBe(true);
    }

    function it_is_stringable(): void
    {
        $bar = new ObjectType('Bar');
        $foo = new ObjectType('Foo');
        $baz = new ObjectType('Baz');
        $intersection = new IntersectionType([$foo, $baz]);

        $this->beConstructedWith([$bar, $intersection]);

        $this->__toString()->shouldBe('\\Bar|(\\Foo&\\Baz)');
    }
}
