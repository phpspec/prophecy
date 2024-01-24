<?php

namespace Tests\Prophecy;

use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\DoubleInterface;
use Prophecy\Prophecy\ProphecySubjectInterface;
use Prophecy\Prophet;

class FunctionalTest extends TestCase
{
    /**
     * @test
     */
    public function case_insensitive_method_names()
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize('ArrayObject');
        $prophecy->offsetGet(1)->willReturn(1)->shouldBeCalledTimes(1);
        $prophecy->offsetget(2)->willReturn(2)->shouldBeCalledTimes(1);
        $prophecy->OffsetGet(3)->willReturn(3)->shouldBeCalledTimes(1);

        $arrayObject = $prophecy->reveal();
        self::assertSame(1, $arrayObject->offsetGet(1));
        self::assertSame(2, $arrayObject->offsetGet(2));
        self::assertSame(3, $arrayObject->offsetGet(3));
    }

    /**
     * @test
     */
    public function it_implements_the_double_interface()
    {
        $prophet = new Prophet();
        $object = $prophet->prophesize('stdClass')->reveal();

        $this->assertInstanceOf(DoubleInterface::class, $object);
    }

    /**
     * @test
     */
    public function it_implements_the_prophecy_subject_interface()
    {
        $prophet = new Prophet();
        $object = $prophet->prophesize('stdClass')->reveal();

        $this->assertInstanceOf(ProphecySubjectInterface::class, $object);
    }
}
