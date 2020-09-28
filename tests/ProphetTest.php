<?php

declare(strict_types=1);

namespace Tests\Prophecy;

use Fixtures\Prophecy\ClassExtendAbstractWithMethodWithReturnType;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;

class ProphetTest extends TestCase
{
    public function testToDoAProhecy()
    {
        $prophet = new Prophet();

        $a = $prophet->prophesize(ClassExtendAbstractWithMethodWithReturnType::class);
        $realObj = $a->reveal();
    }
}
