<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class MyClass
{
}

class ObjectStateTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('getName', 'stdClass');
    }

    function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    /**
     * @param ReflectionClass $reflection
     */
    function it_scores_8_if_argument_object_has_specific_state($reflection)
    {
        $reflection->getName()->willReturn('stdClass');

        $this->scoreArgument($reflection)->shouldReturn(8);
    }

    /**
     * @param ReflectionClass $reflection
     */
    function it_does_not_score_if_argument_state_does_not_match($reflection)
    {
        $reflection->getName()->willReturn('SplFileInfo');

        $this->scoreArgument($reflection)->shouldReturn(false);
    }

    /**
     * @param spec\Prophecy\Argument\Token\MyClass $class
     */
    function it_does_not_score_if_argument_object_does_not_have_method_or_property($class)
    {
        $this->scoreArgument($class)->shouldReturn(false);
    }

    function it_does_not_score_if_argument_is_not_object()
    {
        $this->scoreArgument(42)->shouldReturn(false);
    }

    function it_has_simple_string_representation()
    {
        $this->__toString()->shouldReturn('state(getName(), "stdClass")');
    }
}
