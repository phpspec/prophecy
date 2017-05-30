<?php

namespace spec\Prophecy\Doubler\ClassPatch;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GeneratorPatchSpec extends ObjectBehavior
{
    function it_is_a_patch()
    {
        $this->shouldBeAnInstanceOf('Prophecy\Doubler\ClassPatch\ClassPatchInterface');
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_supports_class_that_implements_only_Generator($node)
    {
        $node->getParentClass()->willReturn('Generator');

        $this->supports($node)->shouldReturn(true);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_does_not_support_class_that_implements_something_else($node)
    {
        $node->getParentClass()->willReturn('Traversable');

        $this->supports($node)->shouldReturn(false);
    }

    function it_has_200_priority()
    {
        $this->getPriority()->shouldReturn(200);
    }

    /**
     * @param \Prophecy\Doubler\Generator\Node\ClassNode $node
     */
    function it_forces_node_to_implement_Generator($node)
    {
        $node->addMethod(Argument::type('Prophecy\Doubler\Generator\Node\MethodNode'))->willReturn(null);

        $this->apply($node);
    }

    function it_actually_works_IRL($node)
    {
        $this->beAnInstanceOf('spec\Prophecy\Doubler\ClassPatch\Test');
        $this->test()->shouldIterateLike(array_combine(range(0, 100), array_map(function($i) { return $i * 2; }, range(0, 100))));
    }

    public function getMatchers()
    {
        return [
            'iterateLike' => function($subject, $expect) {
                return $expect == iterator_to_array($subject);
            },
        ];
    }
}

class Test
{
    public function test()
    {
        foreach (range(0, 100) as $i) {
            yield $i => $i * 2;
        }
    }
}
