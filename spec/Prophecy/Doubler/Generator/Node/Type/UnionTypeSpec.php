<?php

namespace spec\Prophecy\Doubler\Generator\Node\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\IntersectionType;
use Prophecy\Doubler\Generator\Node\Type\SimpleType;
use Prophecy\Doubler\Generator\Node\Type\TypeInterface;
use Prophecy\Doubler\Generator\Node\Type\UnionType;
use Prophecy\Exception\Doubler\DoubleException;

class UnionTypeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            new SimpleType('int'),
            new SimpleType('string'),
        ]);
    }
    function it_implements_type_interface(): void
    {
        $this->shouldImplement(TypeInterface::class);
    }

    function it_throws_double_exception_when_union_type_given(): void
    {
        $this->beConstructedWith([
            new UnionType([new SimpleType('int'), new SimpleType('string')]),
            new SimpleType('bool'),
        ]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_types_duplicated(): void
    {
        $this->beConstructedWith([new SimpleType('string'), new SimpleType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_void(): void
    {
        $this->beConstructedWith([new SimpleType('void'), new SimpleType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_never(): void
    {
        $this->beConstructedWith([new SimpleType('never'), new SimpleType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_mixed(): void
    {
        $this->beConstructedWith([new SimpleType('mixed'), new SimpleType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_throws_double_exception_when_union_with_only_one_type(): void
    {
        $this->beConstructedWith([new SimpleType('string')]);
        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_return_array_of_its_types(): void
    {
        $this->getTypes()->shouldBeLike([
            new SimpleType('int'),
            new SimpleType('string'),
        ]);
    }

    function it_should_accept_simple_type_and_intersection()
    {
        $type1 = new SimpleType('string');
        $type2 = new IntersectionType([new SimpleType('A'), new SimpleType('B')]);
        $this->beConstructedWith([$type1, $type2]);

        $this->has($type1)->shouldBe(true);
        $this->has($type2)->shouldBe(true);
    }
}
