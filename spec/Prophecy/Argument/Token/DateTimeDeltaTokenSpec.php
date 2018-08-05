<?php

namespace spec\Prophecy\Argument\Token;

use PhpSpec\ObjectBehavior;

class DateTimeDeltaTokenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \DateTime(), 12);
    }

    function it_implements_TokenInterface()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Argument\Token\TokenInterface');
    }

    function it_is_not_last()
    {
        $this->shouldNotBeLast();
    }

    function it_scores_true_if_datetime_is_in_delta()
    {
        $d1 = new \DateTime('2017-01-21 12:10:00');
        $d2 = new \DateTime('2017-01-21 12:10:30');

        $this->beConstructedWith($d1, 40);

        $this->scoreArgument($d2)->shouldReturn(true);
    }

    function it_scores_false_if_datetime_is_not_in_delta()
    {
        $d1 = new \DateTime('2017-01-21 12:10:00');
        $d2 = new \DateTime('2017-01-21 12:10:30');

        $this->beConstructedWith($d1, 20);

        $this->scoreArgument($d2)->shouldReturn(false);
    }

    function it_represents_the_object_as_string_with_the_right_format()
    {
        $d = new \DateTime('2017-01-21 12:10:00');

        $this->beConstructedWith($d, 20);

        $this->__toString()->shouldReturn('Date{2017-01-21 12:10:00}~20');
    }
}