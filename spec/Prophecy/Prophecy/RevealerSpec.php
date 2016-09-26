<?php

namespace spec\Prophecy\Prophecy;

use PhpSpec\ObjectBehavior;
use Prophecy\Prophecy\ProphecyInterface;

class RevealerSpec extends ObjectBehavior
{
    function it_is_revealer()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Prophecy\RevealerInterface');
    }

    function it_reveals_single_instance_of_ProphecyInterface(ProphecyInterface $prophecy, \stdClass $object)
    {
        $prophecy->reveal()->willReturn($object);

        $this->reveal($prophecy)->shouldReturn($object);
    }

    function it_reveals_instances_of_ProphecyInterface_inside_array(
        ProphecyInterface $prophecy1,
        ProphecyInterface $prophecy2,
        \stdClass $object1,
        \stdClass $object2
    ) {
        $prophecy1->reveal()->willReturn($object1);
        $prophecy2->reveal()->willReturn($object2);

        $this->reveal(array(
            array('item' => $prophecy2),
            $prophecy1
        ))->shouldReturn(array(
            array('item' => $object2),
            $object1
        ));
    }

    function it_does_not_touch_non_prophecy_interface()
    {
        $this->reveal(42)->shouldReturn(42);
    }
}
