<?php

namespace spec\Prophecy\Comparator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class ProphecyComparatorSpec extends ObjectBehavior
{
    function it_is_a_comparator()
    {
        $this->shouldHaveType('SebastianBergmann\Comparator\ObjectComparator');
    }

    function it_accepts_only_prophecy_objects()
    {
        $this->accepts(123, 321)->shouldReturn(false);
        $this->accepts('string', 'string')->shouldReturn(false);
        $this->accepts(false, true)->shouldReturn(false);
        $this->accepts(true, false)->shouldReturn(false);
        $this->accepts((object)array(), (object)array())->shouldReturn(false);
        $this->accepts(function(){}, (object)array())->shouldReturn(false);
        $this->accepts(function(){}, function(){})->shouldReturn(false);

        $prophet = new Prophet();
        $prophecy = $prophet->prophesize('Prophecy\Prophecy\ObjectProphecy');

        $this->accepts($prophecy, $prophecy)->shouldReturn(true);
    }

    function it_asserts_that_an_object_is_equal_to_its_revealed_prophecy()
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize('Prophecy\Prophecy\ObjectProphecy');

        $this->shouldNotThrow()->duringAssertEquals($prophecy->reveal(), $prophecy);
    }
}
