<?php

namespace spec\Prophecy\Exception\Doubler;

use PHPSpec2\ObjectBehavior;
use spec\Prophecy\Exception\Prophecy;

class ClassCreatorException extends ObjectBehavior
{
    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function let($node)
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
