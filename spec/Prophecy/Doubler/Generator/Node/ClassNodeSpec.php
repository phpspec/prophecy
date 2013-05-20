<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;

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

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method1
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method2
     */
    function it_can_has_methods($method1, $method2)
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

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     */
    function its_hasMethod_returns_true_if_method_exists($method)
    {
        $method->getName()->willReturn('getName');

        $this->addMethod($method);

        $this->hasMethod('getName')->shouldReturn(true);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     */
    function its_getMethod_returns_method_by_name($method)
    {
        $method->getName()->willReturn('getName');

        $this->addMethod($method);

        $this->getMethod('getName')->shouldReturn($method);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     */
    function its_hasMethod_returns_false_if_method_does_not_exists()
    {
        $this->hasMethod('getName')->shouldReturn(false);
    }

    function it_does_not_have_properties_by_default()
    {
        $this->getProperties()->shouldHaveCount(0);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\PropertyNode $property1
     * @param Prophecy\Doubler\Generator\Node\PropertyNode $property2
     */
    function it_is_able_to_have_properties($property1, $property2)
    {
        $property1->getName()->willReturn('title');
        $property2->getName()->willReturn('text');

        $this->addProperty($property1);
        $this->addProperty($property2);

        $this->getProperties()->shouldReturn(array(
            'title' => $property1,
            'text'  => $property2
        ));
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     */
    function its_hasStaticMethods_returns_true_if_a_static_method_exists($method)
    {
        $method->getName()->willReturn('getName');
        $method->isStatic()->willReturn(true);

        $this->addMethod($method);

        $this->hasStaticMethods()->shouldReturn(true);
    }

    /**
     * @param Prophecy\Doubler\Generator\Node\MethodNode $method
     */
    function its_hasStaticMethods_returns_false_if_a_static_method_does_not_exist($method)
    {
        $method->getName()->willReturn('getName');
        $method->isStatic()->willReturn(false);

        $this->addMethod($method);

        $this->hasStaticMethods()->shouldReturn(false);
    }
}
