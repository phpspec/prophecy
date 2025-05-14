<?php

namespace Tests\Prophecy;

use Fixtures\Prophecy\ReturningFinalClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Doubler\DoubleInterface;
use Prophecy\Exception\Prophecy\MethodProphecyException;
use Prophecy\Prophecy\ProphecySubjectInterface;
use Prophecy\Prophet;

class FunctionalTest extends TestCase
{
    #[Test]
    public function case_insensitive_method_names(): void
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

    #[Test]
    public function it_implements_the_double_interface(): void
    {
        $prophet = new Prophet();
        $object = $prophet->prophesize('stdClass')->reveal();

        $this->assertInstanceOf(DoubleInterface::class, $object);
    }

    #[Test]
    public function it_implements_the_prophecy_subject_interface(): void
    {
        $prophet = new Prophet();
        $object = $prophet->prophesize('stdClass')->reveal();

        $this->assertInstanceOf(ProphecySubjectInterface::class, $object);
    }

    public function testUnconfiguredFinalReturnType(): void
    {
        $prophet = new Prophet();
        $object = $prophet->prophesize(ReturningFinalClass::class);

        $object->doSomething()->shouldBeCalled();

        $double = $object->reveal();

        $this->expectException(MethodProphecyException::class);
        $this->expectExceptionMessage('Cannot create a return value for the method. Configure an explicit return value instead.');

        $double->doSomething();
    }
}
