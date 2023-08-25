<?php

namespace Tests\Prophecy\Comparator;

use Prophecy\Comparator\ClosureComparator;
use Prophecy\Comparator\FactoryProvider;
use PHPUnit\Framework\TestCase;

class FactoryProviderTest extends TestCase
{
    /**
     * @test
     */
    function it_should_have_ClosureComparator_registered()
    {
        $comparator = FactoryProvider::getInstance()->getComparatorFor(function(){}, function(){});

        $this->assertInstanceOf(ClosureComparator::class, $comparator);
    }
}
