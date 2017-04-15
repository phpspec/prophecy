<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReflectionPatchSpec extends ObjectBehavior
{

    /**
     * @param Prophecy\Doubler\Generator\Node\ClassNode $class
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     * @param Prophecy\Doubler\Generator\Node\ArgumentNode $argument
     */
    function it_changes_argument_name_when_it_indicates_its_varying($class, $method, $argument)
    {
        $argument->getName()->willReturn('...');
        $argument->setName('__dot_dot_dot__')->shouldBeCalled();

        $class->getMethods()->willReturn(array($method));
        $method->getArguments()->willReturn(array($argument));

        $this->apply($class);
    }
}
