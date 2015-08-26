<?php

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;
use spec\Prophecy\Exception\Prophecy;

class MethodNotExtendableExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('', 'User', 'getName');
    }

    function it_is_DoubleException()
    {
        $this->shouldHaveType('Prophecy\Exception\Doubler\DoubleException');
    }

    function it_has_MethodName()
    {
        $this->getMethodName()->shouldReturn('getName');
    }

    function it_has_classname()
    {
        $this->getClassName()->shouldReturn('User');
    }
}
