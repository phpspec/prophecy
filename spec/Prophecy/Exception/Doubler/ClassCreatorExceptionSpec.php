<?php

namespace spec\Prophecy\Exception\Doubler;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\ClassNode;
use spec\Prophecy\Exception\Prophecy;

class ClassCreatorExceptionSpec extends ObjectBehavior
{
    function let(ClassNode $node)
    {
        $this->beConstructedWith('', $node);
    }

    function it_is_a_prophecy_exception()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Exception');
        $this->shouldBeAnInstanceOf('Prophecy\Exception\Doubler\DoublerException');
    }

    function it_contains_a_reflected_node($node)
    {
        $this->getClassNode()->shouldReturn($node);
    }
}
