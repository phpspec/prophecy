<?php

namespace spec\Prophecy\Comparator;

use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Comparator\Factory;
use SebastianBergmann\Comparator\Factory as BaseFactory;

class FactorySpec extends ObjectBehavior
{
    function let()
    {
        $ref = new \ReflectionClass(BaseFactory::class);

        if ($ref->isFinal()) {
            throw new SkippingException(sprintf('The deprecated "%s" class cannot be used with sebastian/comparator 5+.', Factory::class));
        }
    }

    function it_extends_Sebastian_Comparator_Factory()
    {
        $this->shouldHaveType('SebastianBergmann\Comparator\Factory');
    }

    function it_should_have_ClosureComparator_registered()
    {
        $comparator = $this->getInstance()->getComparatorFor(function () {}, function () {});
        $comparator->shouldHaveType('Prophecy\Comparator\ClosureComparator');
    }
}
