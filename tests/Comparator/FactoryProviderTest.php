<?php

namespace Tests\Prophecy\Comparator;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\Comparator\ClosureComparator;
use Prophecy\Comparator\FactoryProvider;
use PHPUnit\Framework\TestCase;

class FactoryProviderTest extends TestCase
{
    /**
     * @test
     */
    #[Test]
    function it_should_have_ClosureComparator_registered()
    {
        $comparator = FactoryProvider::getInstance()->getComparatorFor(function () {}, function () {});

        $this->assertInstanceOf(ClosureComparator::class, $comparator);
    }
}
