<?php

namespace spec\Prophecy\Comparator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClosureComparatorSpec extends ObjectBehavior
{
    function it_is_comparator()
    {
        $this->shouldHaveType('SebastianBergmann\Comparator\Comparator');
    }

    function it_accepts_only_closures()
    {
        $this->accepts(123, 321)->shouldReturn(false);
        $this->accepts('string', 'string')->shouldReturn(false);
        $this->accepts(false, true)->shouldReturn(false);
        $this->accepts(true, false)->shouldReturn(false);
        $this->accepts((object)array(), (object)array())->shouldReturn(false);
        $this->accepts(function(){}, (object)array())->shouldReturn(false);
        $this->accepts(function(){}, (object)array())->shouldReturn(false);

        $this->accepts(function(){}, function(){})->shouldReturn(true);
    }

    function it_asserts_that_all_closures_are_different()
    {
        $this->shouldThrow()->duringAssertEquals(function(){}, function(){});
    }

    function it_asserts_that_all_closures_are_different_even_if_its_the_same_closure()
    {
        $closure = function(){};

        $this->shouldThrow()->duringAssertEquals($closure, $closure);
    }
}
