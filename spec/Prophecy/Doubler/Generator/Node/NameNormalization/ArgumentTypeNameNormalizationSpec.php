<?php

namespace spec\Prophecy\Doubler\Generator\Node\NameNormalization;

use PhpSpec\ObjectBehavior;
use Prophecy\Exception\Doubler\DoubleException;

class ArgumentTypeNameNormalizationSpec extends ObjectBehavior
{
    function it_has_no_types_at_start()
    {
        $this->normalize()->shouldReturn([]);
    }

    function it_can_have_a_simple_type()
    {
        $this->normalize('int')->shouldReturn(['int']);
    }

    function it_can_have_multiple_types()
    {
        $this->normalize('int', 'string')->shouldReturn(['int', 'string']);
    }

    function it_will_prefix_fcqns()
    {
        $this->normalize('Foo')->shouldReturn(['\\Foo']);
    }

    function it_will_not_prefix_fcqns_that_already_have_prefix()
    {
        $this->beConstructedWith();

        $this->normalize('\\Foo')->shouldReturn(['\\Foo']);
    }

    function it_does_not_prefix_false()
    {
        $this->beConstructedWith();

        $this->normalize('false', 'array')->shouldReturn(['false', 'array']);
    }
}
