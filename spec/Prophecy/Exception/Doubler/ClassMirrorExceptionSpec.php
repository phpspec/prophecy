<?php

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;

class ClassMirrorExceptionSpec extends ObjectBehavior
{
    function let(\ReflectionClass $class)
    {
        $this->beConstructedWith('', $class);
    }

    function it_is_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Exception');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\DoublerException');
    }

    function it_contains_a_reflected_class_link($class)
    {
        $this->getReflectedClass()->shouldReturn($class);
    }
}
