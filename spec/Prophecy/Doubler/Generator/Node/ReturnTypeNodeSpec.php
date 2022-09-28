<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use Fixtures\Prophecy\UnionReturnTypes;
use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\Type\IntersectionTypeNode;
use Prophecy\Doubler\Generator\Node\Type\NamedTypeNode;
use Prophecy\Doubler\Generator\Node\Type\UnionTypeNode;
use Prophecy\Exception\Doubler\DoubleException;

class ReturnTypeNodeSpec extends ObjectBehavior
{
    function it_has_no_return_types_at_start()
    {
        $this->getType()->shouldReturn(null);
    }

    function it_can_have_a_simple_type() {
        $node = new NamedTypeNode('int', false, true);
        $this->beConstructedWith($node);
        $this->getType()->shouldReturn($node);
    }

    function it_can_have_multiple_union_types()
    {
        $int    = new NamedTypeNode('int', false, true);
        $string = new NamedTypeNode('string', false, true);
        $union  = new UnionTypeNode(false, $int, $string);
        $this->beConstructedWith($union);

        $this->getType()->shouldReturn($union);
    }

    function it_can_have_multiple_intersection_types()
    {
        $int           = new NamedTypeNode('int', false, true);
        $string        = new NamedTypeNode('string', false, true);
        $intersection  = new IntersectionTypeNode(false, $int, $string);
        $this->beConstructedWith($intersection);

        $this->getType()->shouldReturn($intersection);
    }

    function it_can_have_void_type()
    {
        $void = new NamedTypeNode('void', false, true);
        $this->beConstructedWith($void);

        $this->getType()->shouldReturn($void);
    }

    function it_can_use_shorthand_null_syntax_if_it_is_named_type_node_and_allows_null()
    {
        $int = new NamedTypeNode('int', true, true);
        $this->beConstructedWith($int);

        $this->canUseNullShorthand()->shouldReturn(true);
    }

    function it_can_not_use_shorthand_if_its_not_named_type_node()
    {
        $int           = new NamedTypeNode('int', false, true);
        $string        = new NamedTypeNode('string', false, true);
        $intersection  = new IntersectionTypeNode(false, $int, $string);
        $this->beConstructedWith($intersection);

        $this->canUseNullShorthand()->shouldReturn(false);
    }

    function it_can_not_use_shorthand_if_its_named_type_node_but_does_not_allow_null()
    {
        $int = new NamedTypeNode('int', false, true);
        $this->beConstructedWith($int);

        $this->canUseNullShorthand()->shouldReturn(false);
    }

    function it_does_not_allow_union_void()
    {
        $void  = new NamedTypeNode('void', false, true);
        $int   = new NamedTypeNode('int', false, true);
        $union = new UnionTypeNode(false, $void, $int);
        $this->beConstructedWith($union);

        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_does_not_allow_union_mixed()
    {
        $void  = new NamedTypeNode('mixed', false, true);
        $int   = new NamedTypeNode('int', false, true);
        $union = new UnionTypeNode(false, $void, $int);

        $this->beConstructedWith($union);

        if (PHP_VERSION_ID >=80000) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }
    }

    function it_does_not_prefix_false_in_a_union()
    {
        $array = new NamedTypeNode('array', false, true);
        $false = new NamedTypeNode('false', false, true);
        $union = new UnionTypeNode(false, $array, $false);
        $this->beConstructedWith($union);

        $this->getType()->getTypes()[0]->getName()->shouldReturn('array');
    }

    function it_does_not_allow_standalone_false()
    {
        $false = new NamedTypeNode('false', false, true);
        $this->beConstructedWith($false);

        if (PHP_VERSION_ID >=80000) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }
    }

    function it_does_not_allow_nullable_false()
    {
        $false = new NamedTypeNode('false', true, true);
        $this->beConstructedWith($false);

        if (PHP_VERSION_ID >=80000) {
            $this->shouldThrow(DoubleException::class)->duringInstantiation();
        }
    }

    function it_does_not_prefix_never()
    {
        $never = new NamedTypeNode('never', false, true);
        $this->beConstructedWith($never);

        $this->getType()->getName()->shouldBe('never');
    }

    function it_does_not_allow_union_never()
    {
        $never = new NamedTypeNode('never', false, true);
        $int   = new NamedTypeNode('int', false, true);
        $union = new UnionTypeNode(false, $never, $int);

        $this->beConstructedWith($union);

        $this->shouldThrow(DoubleException::class)->duringInstantiation();
    }

    function it_has_a_return_statement_if_it_is_a_simple_type()
    {
        $int = new NamedTypeNode('int', false, true);
        $this->beConstructedWith($int);

        $this->shouldHaveReturnStatement();
    }

    function it_does_not_have_return_statement_if_it_returns_void()
    {
        $void = new NamedTypeNode('void', false, true);
        $this->beConstructedWith($void);

        $this->shouldNotHaveReturnStatement();
    }

    function it_does_not_have_return_statement_if_it_returns_never()
    {
        $never = new NamedTypeNode('never', false, true);
        $this->beConstructedWith($never);

        $this->shouldNotHaveReturnStatement();
    }
}
