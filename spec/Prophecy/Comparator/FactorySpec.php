<?php

namespace spec\Prophecy\Comparator;

use PhpSpec\ObjectBehavior;

class FactorySpec extends ObjectBehavior
{
    function it_extends_Sebastian_Comparator_Factory()
    {
        $this->shouldHaveType('SebastianBergmann\Comparator\Factory');
    }

    function it_should_have_ClosureComparator_registered()
    {
        $comparator = $this->getInstance()->getComparatorFor(function(){}, function(){});
        $comparator->shouldHaveType('Prophecy\Comparator\ClosureComparator');
    }
}
