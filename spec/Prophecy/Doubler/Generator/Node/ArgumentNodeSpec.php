<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;

class ArgumentNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('name');
    }

    function it_is_not_be_passed_by_reference_by_default()
    {
        $this->shouldNotBePassedByReference();
    }

    function it_is_passed_by_reference_if_marked()
    {
        $this->setAsPassedByReference();
        $this->shouldBePassedByReference();
    }

    function it_is_not_variadic_by_default()
    {
        $this->shouldNotBeVariadic();
    }

    function it_is_variadic_if_marked()
    {
        $this->setAsVariadic();
        $this->shouldBeVariadic();
    }

    function it_does_not_have_default_by_default()
    {
        $this->shouldNotHaveDefault();
    }

    function it_does_not_have_default_if_variadic()
    {
        $this->setDefault(null);
        $this->setAsVariadic();
        $this->shouldNotHaveDefault();
    }

    function it_does_have_default_if_not_variadic()
    {
        $this->setDefault(null);
        $this->setAsVariadic(false);
        $this->hasDefault()->shouldReturn(true);
    }

    function it_has_name_with_which_it_was_been_constructed()
    {
        $this->getName()->shouldReturn('name');
    }

    function it_has_no_typehint_by_default()
    {
        $this->getTypeHint()->shouldReturn(null);
    }

    function its_typeHint_is_mutable_with_deprecated_accessors()
    {
        $this->setTypeHint('array');
        $this->getTypeHint()->shouldReturn('array');
    }

    function it_can_set_nullable_type_using_deprecated_method()
    {
        $this->setTypeHint('int');

        $this->setAsNullable();

        $this->shouldBeNullable();
    }

    function it_can_unset_nullable_type_using_deprecated_method()
    {
        $this->setTypeHint('int');

        $this->setAsNullable(false);

        $this->shouldNotBeNullable();
    }

    function it_has_an_empty_type_by_default()
    {
        $this->getTypeNode()->shouldBeLike(new ArgumentTypeNode());
    }

    function it_has_a_mutable_type()
    {
        $this->setTypeNode(new ArgumentTypeNode('int'));

        $this->getTypeNode()->shouldBeLike(new ArgumentTypeNode('int'));
    }

    function it_does_not_have_default_value_by_default()
    {
        $this->getDefault()->shouldReturn(null);
    }

    function it_is_not_optional_by_default()
    {
        $this->isOptional()->shouldReturn(false);
    }

    function its_default_is_mutable()
    {
        $this->setDefault(array());
        $this->getDefault()->shouldReturn(array());
    }

    function it_is_marked_as_optional_when_default_is_set()
    {
        $this->setDefault(null);
        $this->isOptional()->shouldReturn(true);
    }
}
