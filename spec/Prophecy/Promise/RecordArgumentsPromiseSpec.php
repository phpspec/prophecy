<?php

namespace spec\Prophecy\Promise;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecordArgumentsPromiseSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Prophecy\Promise\RecordArgumentsPromise');
    }

    /**
     * @param Prophecy\Prophecy\ObjectProphecy $object
     * @param Prophecy\Prophecy\MethodProphecy $method
     */
    function it_should_store_arguments($object, $method)
    {
        $arguments = array('one', 'two');
        $this->execute($arguments, $object, $method)->shouldReturn(null);

        // Retrieved arguments should be the same as the passed arguments
        $this->getArguments()->shouldBeEqualTo($arguments);
    }
}
