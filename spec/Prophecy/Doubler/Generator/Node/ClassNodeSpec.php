<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\MethodNode;
use Prophecy\Exception\Doubler\MethodNotExtendableException;

class ClassNodeSpec extends ObjectBehavior
{
    function its_parentClass_is_a_stdClass_by_default()
    {
        $this->getParentClass()->shouldReturn('stdClass');
    }

    function its_parentClass_is_mutable()
    {
        $this->setParentClass('Exception');
        $this->getParentClass()->shouldReturn('Exception');
    }

    function its_parentClass_is_set_to_stdClass_if_user_set_null()
    {
        $this->setParentClass(null);
        $this->getParentClass()->shouldReturn('stdClass');
    }

    function it_does_not_implement_any_interface_by_default()
    {
        $this->getInterfaces()->shouldHaveCount(0);
    }

    function its_addInterface_adds_item_to_the_list_of_implemented_interfaces()
    {
        $this->addInterface('MyInterface');
        $this->getInterfaces()->shouldHaveCount(1);
    }

    function its_hasInterface_returns_true_if_class_implements_interface()
    {
        $this->addInterface('MyInterface');
        $this->hasInterface('MyInterface')->shouldReturn(true);
    }

    function its_hasInterface_returns_false_if_class_does_not_implements_interface()
    {
        $this->hasInterface('MyInterface')->shouldReturn(false);
    }

    function it_supports_implementation_of_multiple_interfaces()
    {
        $this->addInterface('MyInterface');
        $this->addInterface('MySecondInterface');
        $this->getInterfaces()->shouldHaveCount(2);
    }

    function it_ignores_same_interfaces_added_twice()
    {
        $this->addInterface('MyInterface');
        $this->addInterface('MyInterface');

        $this->getInterfaces()->shouldHaveCount(1);
        $this->getInterfaces()->shouldReturn(array('MyInterface'));
    }

    function it_does_not_have_methods_by_default()
    {
        $this->getMethods()->shouldHaveCount(0);
    }

    function it_can_has_methods(MethodNode $method1, MethodNode $method2)
    {
        $method1->getName()->willReturn('__construct');
        $method2->getName()->willReturn('getName');

        $this->addMethod($method1);
        $this->addMethod($method2);

        $this->getMethods()->shouldReturn(array(
            '__construct' => $method1,
            'getName'     => $method2
        ));
    }

    function its_hasMethod_returns_true_if_method_exists(MethodNode $method)
    {
        $method->getName()->willReturn('getName');

        $this->addMethod($method);

        $this->hasMethod('getName')->shouldReturn(true);
    }

    function its_getMethod_returns_method_by_name(MethodNode $method)
    {
        $method->getName()->willReturn('getName');

        $this->addMethod($method);

        $this->getMethod('getName')->shouldReturn($method);
    }

    function its_hasMethod_returns_false_if_method_does_not_exists()
    {
        $this->hasMethod('getName')->shouldReturn(false);
    }

    function its_hasMethod_returns_false_if_method_has_been_removed(MethodNode $method)
    {
        $method->getName()->willReturn('getName');
        $this->addMethod($method);
        $this->removeMethod('getName');

        $this->hasMethod('getName')->shouldReturn(false);
    }


    function it_does_not_have_properties_by_default()
    {
        $this->getProperties()->shouldHaveCount(0);
    }

    function it_is_able_to_have_properties()
    {
        $this->addProperty('title');
        $this->addProperty('text', 'private');
        $this->getProperties()->shouldReturn(array(
            'title' => 'public',
            'text'  => 'private'
        ));
    }

    function its_addProperty_does_not_accept_unsupported_visibility()
    {
        $this->shouldThrow('InvalidArgumentException')->duringAddProperty('title', 'town');
    }

    function its_addProperty_lowercases_visibility_before_setting()
    {
        $this->addProperty('text', 'PRIVATE');
        $this->getProperties()->shouldReturn(array('text' => 'private'));
    }

    function its_has_no_unextendable_methods_by_default()
    {
        $this->getUnextendableMethods()->shouldHaveCount(0);
    }

    function its_addUnextendableMethods_adds_an_unextendable_method()
    {
        $this->addUnextendableMethod('testMethod');
        $this->getUnextendableMethods()->shouldHaveCount(1);
    }

    function its_methods_are_extendable_by_default()
    {
        $this->isExtendable('testMethod')->shouldReturn(true);
    }

    function its_unextendable_methods_are_not_extendable()
    {
        $this->addUnextendableMethod('testMethod');
        $this->isExtendable('testMethod')->shouldReturn(false);
    }

    function its_addUnextendableMethods_doesnt_create_duplicates()
    {
        $this->addUnextendableMethod('testMethod');
        $this->addUnextendableMethod('testMethod');
        $this->getUnextendableMethods()->shouldHaveCount(1);
    }

    function it_throws_an_exception_when_adding_a_method_that_isnt_extendable(MethodNode $method)
    {
        $this->addUnextendableMethod('testMethod');
        $method->getName()->willReturn('testMethod');

        $expectedException = new MethodNotExtendableException(
            "Method `testMethod` is not extendable, so can not be added.",
            "stdClass",
            "testMethod"
        );
        $this->shouldThrow($expectedException)->duringAddMethod($method);
    }
}
