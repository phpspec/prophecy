<?php

namespace spec\Prophecy\Doubler;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\ClassPatch\ClassPatchInterface;
use Prophecy\Doubler\Generator\ClassCreator;
use Prophecy\Doubler\Generator\ClassMirror;
use Prophecy\Doubler\Generator\Node\ClassNode;
use Prophecy\Doubler\NameGenerator;

class CachedDoublerSpec extends ObjectBehavior
{
    function let(ClassMirror $mirror, ClassCreator $creator, NameGenerator $namer)
    {
        $this->beConstructedWith($mirror, $creator, $namer);
        $this->resetCache();
    }

    /**
     * @todo implement
     * T - T
     * T - F
     * F - T
     * F - F
     * F T F
     * F T T
     */

    // T - -
    function it_creates_only_one_class_definition_for_the_same_class_without_interfaces_and_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class,
        ClassNode $node
    ) {
        $mirror->reflect($class, array())->willReturn($node);
        $namer->name($class, array())->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');

        $creator->create('SplStack', $node)->shouldBeCalledTimes(1);

        $this->double($class, array());
        $this->double($class, array());
    }

    // F - -
    function it_creates_two_class_definitions_for_different_classes_without_interfaces_and_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class1,
        \ReflectionClass $class2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class1, array())->willReturn($node1);
        $mirror->reflect($class2, array())->willReturn($node2);
        $namer->name($class1, array())->willReturn('SplStack');
        $namer->name($class2, array())->willReturn('spec\Prophecy\Doubler\aClass');
        $class1->getName()->willReturn('stdClass');
        $class2->getName()->willReturn('aClass');

        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('spec\Prophecy\Doubler\aClass', $node2)->shouldBeCalledTimes(1);

        $this->double($class1, array());
        $this->double($class2, array());
    }

    // T F T
    function it_creates_two_different_class_definitions_for_the_same_class_with_different_interfaces_and_same_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class, array($interface1))->willReturn($node1);
        $mirror->reflect($class, array($interface2))->willReturn($node2);
        $alt1->supports($node1)->willReturn(true);
        $alt1->supports($node2)->willReturn(true);
        $alt2->supports($node1)->willReturn(false);
        $alt2->supports($node2)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class, array($interface1))->willReturn('SplStack');
        $namer->name($class, array($interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node1)->shouldBeCalled();
        $alt1->apply($node2)->shouldBeCalled();
        $alt2->apply($node1)->shouldNotBeCalled();
        $alt2->apply($node2)->shouldNotBeCalled();
        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('SplStack', $node2)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->registerClassPatch($alt2);

        $this->double($class, array($interface1));
        $this->double($class, array($interface2));
    }

    // F F T
    function it_creates_two_different_class_definitions_for_different_classes_with_different_interfaces_and_same_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class1,
        \ReflectionClass $class2,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class1, array($interface1))->willReturn($node1);
        $mirror->reflect($class2, array($interface2))->willReturn($node2);
        $alt1->supports($node1)->willReturn(true);
        $alt1->supports($node2)->willReturn(true);
        $alt2->supports($node1)->willReturn(false);
        $alt2->supports($node2)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class1, array($interface1))->willReturn('SplStack');
        $namer->name($class2, array($interface2))->willReturn('spec\Prophecy\Doubler\aClass');
        $class1->getName()->willReturn('stdClass');
        $class2->getName()->willReturn('aClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node1)->shouldBeCalled();
        $alt1->apply($node2)->shouldBeCalled();
        $alt2->apply($node1)->shouldNotBeCalled();
        $alt2->apply($node2)->shouldNotBeCalled();
        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('spec\Prophecy\Doubler\aClass', $node2)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->registerClassPatch($alt2);

        $this->double($class1, array($interface1));
        $this->double($class2, array($interface2));
    }

    // T T -
    function it_creates_only_one_class_definition_for_the_same_class_with_same_interfaces_and_without_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node
    ) {
        $mirror->reflect($class, array($interface1, $interface2))->willReturn($node);
        $namer->name($class, array($interface1, $interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $creator->create('SplStack', $node)->shouldBeCalledTimes(1);

        $this->double($class, array($interface1, $interface2));
        $this->double($class, array($interface1, $interface2));
    }

    // F T -
    function it_creates_only_one_class_definition_for_different_classes_with_same_interfaces_and_without_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class1,
        \ReflectionClass $class2,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class1, array($interface1, $interface2))->willReturn($node1);
        $mirror->reflect($class2, array($interface1, $interface2))->willReturn($node2);
        $namer->name($class1, array($interface1, $interface2))->willReturn('SplStack');
        $namer->name($class2, array($interface1, $interface2))->willReturn('spec\Prophecy\Doubler\aClass');
        $class1->getName()->willReturn('stdClass');
        $class2->getName()->willReturn('aClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('spec\Prophecy\Doubler\aClass', $node2)->shouldBeCalledTimes(1);

        $this->double($class1, array($interface1, $interface2));
        $this->double($class2, array($interface1, $interface2));
    }

    // T F -
    function it_creates_two_different_class_definitions_for_the_same_class_with_different_interfaces_and_without_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class, array($interface1))->willReturn($node1);
        $mirror->reflect($class, array($interface2))->willReturn($node2);
        $namer->name($class, array($interface1))->willReturn('SplStack');
        $namer->name($class, array($interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('SplStack', $node2)->shouldBeCalledTimes(1);

        $this->double($class, array($interface1));
        $this->double($class, array($interface2));
    }

    // F F -
    function it_creates_two_different_class_definitions_for_different_classes_with_different_interfaces_and_without_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        \ReflectionClass $class1,
        \ReflectionClass $class2,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class1, array($interface1))->willReturn($node1);
        $mirror->reflect($class2, array($interface2))->willReturn($node2);
        $namer->name($class1, array($interface1))->willReturn('SplStack');
        $namer->name($class2, array($interface2))->willReturn('spec\Prophecy\Doubler\aClass');
        $class1->getName()->willReturn('stdClass');
        $class2->getName()->willReturn('aClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('spec\Prophecy\Doubler\aClass', $node2)->shouldBeCalledTimes(1);

        $this->double($class1, array($interface1));
        $this->double($class2, array($interface2));
    }

    // T T T
    function it_creates_only_one_class_definition_for_the_same_class_with_same_interfaces_and_same_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
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
        $creator->create('SplStack', $node)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->registerClassPatch($alt2);

        $this->double($class, array($interface1, $interface2));
        $this->double($class, array($interface1, $interface2));
    }

    // F F F
    function it_creates_two_class_definitions_for_different_classes_with_different_interfaces_and_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class1,
        \ReflectionClass $class2,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class1, array($interface1))->willReturn($node1);
        $mirror->reflect($class2, array($interface2))->willReturn($node2);
        $alt1->supports($node1)->willReturn(true);
        $alt1->supports($node2)->willReturn(true);
        $alt2->supports($node2)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class1, array($interface1))->willReturn('SplStack');
        $namer->name($class2, array($interface2))->willReturn('spec\Prophecy\Doubler\aClass');
        $class1->getName()->willReturn('stdClass');
        $class2->getName()->willReturn('aClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node1)->shouldBeCalled();
        $alt1->apply($node2)->shouldBeCalled();
        $alt2->apply($node2)->shouldNotBeCalled();
        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('spec\Prophecy\Doubler\aClass', $node2)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->double($class1, array($interface1));

        $this->registerClassPatch($alt2);
        $this->double($class2, array($interface2));
    }

    // T F F
    function it_creates_two_class_definitions_for_the_same_class_with_different_interfaces_and_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class, array($interface1))->willReturn($node1);
        $mirror->reflect($class, array($interface2))->willReturn($node2);
        $alt1->supports($node1)->willReturn(true);
        $alt1->supports($node2)->willReturn(true);
        $alt2->supports($node2)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class, array($interface1))->willReturn('SplStack');
        $namer->name($class, array($interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node1)->shouldBeCalled();
        $alt1->apply($node2)->shouldBeCalled();
        $alt2->apply($node2)->shouldNotBeCalled();
        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);
        $creator->create('SplStack', $node2)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->double($class, array($interface1));

        $this->registerClassPatch($alt2);
        $this->double($class, array($interface2));
    }

    // T T F
    function it_creates_two_different_class_definitions_for_the_same_class_with_same_interfaces_and_different_patches(
        ClassMirror $mirror,
        ClassCreator $creator,
        NameGenerator $namer,
        ClassPatchInterface $alt1,
        ClassPatchInterface $alt2,
        \ReflectionClass $class,
        \ReflectionClass $interface1,
        \ReflectionClass $interface2,
        ClassNode $node1,
        ClassNode $node2
    ) {
        $mirror->reflect($class, array($interface1, $interface2))->willReturn($node1, $node2);
        $alt1->supports($node1)->willReturn(true);
        $alt1->supports($node2)->willReturn(true);
        $alt2->supports($node2)->willReturn(false);
        $alt1->getPriority()->willReturn(1);
        $alt2->getPriority()->willReturn(2);
        $namer->name($class, array($interface1, $interface2))->willReturn('SplStack');
        $class->getName()->willReturn('stdClass');
        $interface1->getName()->willReturn('ArrayAccess');
        $interface2->getName()->willReturn('Iterator');

        $alt1->apply($node1)->shouldBeCalled();
        $creator->create('SplStack', $node1)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt1);
        $this->double($class, array($interface1, $interface2));

        $alt1->apply($node2)->shouldBeCalled();
        $alt2->apply($node2)->shouldNotBeCalled();
        $creator->create('SplStack', $node2)->shouldBeCalledTimes(1);

        $this->registerClassPatch($alt2);
        $this->double($class, array($interface1, $interface2));
    }
}

class aClass
{
}