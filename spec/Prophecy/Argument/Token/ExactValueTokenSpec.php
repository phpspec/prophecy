<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class ExactValueTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(42);
    }

    function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    function it_holds_value()
    {
        $this->getValue()->shouldReturn(42);
    }

    function it_scores_10_if_value_is_equal_to_argument()
    {
        $this->scoreArgument(42)->shouldReturn(10);
    }

    function it_does_not_scores_if_value_is_not_equal_to_argument()
    {
        $this->scoreArgument(50)->shouldReturn(false);
    }

    function it_generates_proper_string_representation_for_integer()
    {
        $this->beConstructedWith(42);
        $this->__toString()->shouldReturn('exact(42)');
    }

    function it_generates_proper_string_representation_for_string()
    {
        $this->beConstructedWith('some string');
        $this->__toString()->shouldReturn('exact("some string")');
    }

    function it_generates_single_line_representation_for_multiline_string()
    {
        $this->beConstructedWith("some\nstring");
        $this->__toString()->shouldReturn('exact("some\\nstring")');
    }

    function it_generates_proper_string_representation_for_double()
    {
        $this->beConstructedWith(42.3);
        $this->__toString()->shouldReturn('exact(42.3)');
    }

    function it_generates_proper_string_representation_for_boolean_true()
    {
        $this->beConstructedWith(true);
        $this->__toString()->shouldReturn('exact(true)');
    }

    function it_generates_proper_string_representation_for_boolean_false()
    {
        $this->beConstructedWith(false);
        $this->__toString()->shouldReturn('exact(false)');
    }

    function it_generates_proper_string_representation_for_null()
    {
        $this->beConstructedWith(null);
        $this->__toString()->shouldReturn('exact(null)');
    }

    function it_generates_proper_string_representation_for_empty_array()
    {
        $this->beConstructedWith(array());
        $this->__toString()->shouldReturn('exact([])');
    }

    function it_generates_proper_string_representation_for_array()
    {
        $this->beConstructedWith(array('zet', 42));
        $this->__toString()->shouldReturn('exact(["zet", 42])');
    }

    function it_generates_proper_string_representation_for_resource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->beConstructedWith($resource);
        $this->__toString()->shouldReturn('exact(stream:'.$resource.')');
    }

    /**
     * @param stdClass $object
     */
    function it_generates_proper_string_representation_for_object($object)
    {
        $objHash = sprintf('%s:%s',
            get_class($object->getWrappedObject()),
            spl_object_hash($object->getWrappedObject())
        );

        $this->beConstructedWith($object);
        $this->__toString()->shouldReturn("exact($objHash)");
    }
}
