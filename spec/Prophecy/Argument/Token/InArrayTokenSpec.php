<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\TokenInterface;

class InArrayTokenSpec extends ObjectBehavior
{
    function it_implements_TokenInterface()
    {
        $this->beConstructedWith(array());
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->beConstructedWith(array());
        $this->shouldNotBeLast();
    }

    function it_scores_8_if_argument_is_in_array()
    {
        $this->beConstructedWith(array(1, 2, 3));
        $this->scoreArgument(2)->shouldReturn(8);
    }

    function it_scores_false_if_argument_is_not_in_array()
    {
        $this->beConstructedWith(array(1, 2, 3));
        $this->scoreArgument(5)->shouldReturn(false);
    }

    function it_generates_array_in_string_format()
    {
        $this->beConstructedWith(array(1, 2, 3));
        $this->__toString()->shouldBe('[1, 2, 3]');
    }

    function it_generates_an_empty_array_as_string_when_token_is_empty()
    {
        $this->beConstructedWith(array());
        $this->__toString()->shouldBe('[]');
    }
}
