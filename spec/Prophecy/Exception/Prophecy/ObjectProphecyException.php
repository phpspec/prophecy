<?php

namespace spec\Prophecy\Exception\Prophecy;

use PHPSpec2\ObjectBehavior;
use spec\Prophecy\Exception\Prophecy;

class ObjectProphecyException extends ObjectBehavior
{
    /**
     * @param Prophecy\Prophecy\ObjectProphecy $objectProphecy
     */
    function let($objectProphecy)
    {
        $this->beConstructedWith('message', $objectProphecy);
    }

    function it_should_be_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Prophecy\ProphecyException');
    }

    function it_holds_double_reference($objectProphecy)
    {
        $this->getObjectProphecy()->shoulReturn($objectProphecy);
    }
}
