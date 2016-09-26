<?php

namespace spec\Prophecy\Doubler;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\ClassPatch\ClassPatchInterface;
use Prophecy\Doubler\Generator\ClassCreator;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\NameGenerator;

class DoublerSpec extends ObjectBehavior
{
    function let(ClassMirror $mirror, ClassCreator $creator, NameGenerator $namer)
    {
        $this->beConstructedWith($mirror, $creator, $namer);
    }

    function it_does_not_have_patches_by_default()
    {
        $this->getClassPatches()->shouldHaveCount(0);
    }

    function its_registerClassPatch_adds_a_patch_to_the_doubler(ClassPatchInterface $patch)
    {
        $this->registerClassPatch($patch);
        $this->getClassPatches()->shouldReturn(array($patch));
    }

    function its_getClassPatches_sorts_patches_by_priority(
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        ClassPatchInterface $alt3,
        ClassPatchInterface $alt4
    ) {
        $alt1->getPriority()->willReturn(2);
        $alt2->getPriority()->willReturn(50);
        $alt3->getPriority()->willReturn(10);
        $alt4->getPriority()->willReturn(0);

        $this->registerClassPatch($alt1);
        $this->registerClassPatch($alt2);
        $this->registerClassPatch($alt3);
        $this->registerClassPatch($alt4);

        $this->getClassPatches()->shouldReturn(array($alt2, $alt3, $alt1, $alt4));
    }

    function its_double_mirrors_alterates_and_instantiates_provided_class(
        $mirror,
        $creator,
        $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node
    ) {
        $mirror->reflect($class, array($interface1, $interface2))->willReturn($node);
        $alt1->supports($node)->willReturn(true);
        $alt2->supports($node)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class, array($interface1, $interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node)->shouldBeCalled();
        $alt2->apply($node)->shouldNotBeCalled();
        $creator->create('SplStack', $node)->shouldBeCalled();

        $this->registerClassPatch($alt1);
        $this->registerClassPatch($alt2);

        $this->double($class, array($interface1, $interface2))
            ->shouldReturnAnInstanceOf('SplStack');
    }

    function it_double_instantiates_a_class_with_constructor_argument(
        $mirror,
        \ReflectionClass $class,
        ClassNode $node,
        $namer
    ) {
        $class->getName()->willReturn('ReflectionClass');
        $mirror->reflect($class, array())->willReturn($node);
        $namer->name($class, array())->willReturn('ReflectionClass');

        $double = $this->double($class, array(), array('stdClass'));
        $double->shouldBeAnInstanceOf('ReflectionClass');
        $double->getName()->shouldReturn('stdClass');
    }

    function it_can_instantiate_class_with_final_constructor(
        $mirror,
        \ReflectionClass $class,
        ClassNode $node,
        $namer
    ) {
        $class->getName()->willReturn('spec\Prophecy\Doubler\WithFinalConstructor');
        $mirror->reflect($class, array())->willReturn($node);
        $namer->name($class, array())->willReturn('spec\Prophecy\Doubler\WithFinalConstructor');

        $double = $this->double($class, array());

        $double->shouldBeAnInstanceOf('spec\Prophecy\Doubler\WithFinalConstructor');
    }
}

class WithFinalConstructor
{
    final public function __construct() {}
}
