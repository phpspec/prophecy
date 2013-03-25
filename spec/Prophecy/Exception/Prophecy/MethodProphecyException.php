<?php

namespace spec\Prophecy\Exception\Prophecy;

use PHPSpec2\ObjectBehavior;
use spec\Prophecy\Exception\Prophecy;

class MethodProphecyException extends ObjectBehavior
{
    /**
     * @param Prophecy\Prophecy\ObjectProphecy $objectProphecy
     * @param Prophecy\Prophecy\MethodProphecy   $methodProphecy
     */
    function let($objectProphecy, $methodProphecy)
    {
        $methodProphecy->getObjectProphecy()->willReturn($objectProphecy);

        $this->beConstructedWith('message', $methodProphecy);
    }

    function it_extends_DoubleException()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prophecy\ObjectProphecyException');
    }

    function it_holds_a_stub_reference($methodProphecy)
    {
        $this->getMethodProphecy()->shoulReturn($methodProphecy);
    }
}
