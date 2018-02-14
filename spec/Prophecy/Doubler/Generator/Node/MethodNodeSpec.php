<?php

namespace spec\Prophecy\Doubler\Generator\Node;

use PhpSpec\ObjectBehavior;
use Prophecy\Doubler\Generator\Node\ArgumentNode;

class MethodNodeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('getTitle');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('getTitle');
    }

    function it_has_public_visibility_by_default()
    {
        $this->getVisibility()->shouldReturn('public');
    }

    function its_visibility_is_mutable()
    {
        $this->setVisibility('private');
        $this->getVisibility()->shouldReturn('private');
    }

    function it_is_not_static_by_default()
    {
        $this->shouldNotBeStatic();
    }

    function it_does_not_return_a_reference_by_default()
    {
        $this->returnsReference()->shouldReturn(false);
    }

    function it_should_be_settable_as_returning_a_reference_through_setter()
    {
        $this->setReturnsReference();
        $this->returnsReference()->shouldReturn(true);
    }

    function it_should_be_settable_as_static_through_setter()
    {
        $this->setStatic();
        $this->shouldBeStatic();
    }

    function it_accepts_only_supported_visibilities()
    {
        $this->shouldThrow('InvalidArgumentException')->duringSetVisibility('stealth');
    }

    function it_lowercases_visibility_before_setting_it()
    {
        $this->setVisibility('Public');
        $this->getVisibility()->shouldReturn('public');
    }

    function its_useParentCode_causes_method_to_call_parent(ArgumentNode $argument1, ArgumentNode $argument2)
    {
        $argument1->getName()->willReturn('objectName');
        $argument2->getName()->willReturn('default');

        $argument1->isVariadic()->willReturn(false);
        $argument2->isVariadic()->willReturn(true);

        $this->addArgument($argument1);
        $this->addArgument($argument2);

        $this->useParentCode();

        $this->getCode()->shouldReturn(
            'return parent::getTitle($objectName, ...$default);'
        );
    }

    function its_code_is_mutable()
    {
        $this->setCode('echo "code";');
        $this->getCode()->shouldReturn('echo "code";');
    }

    function its_reference_returning_methods_will_generate_exceptions()
    {
        $this->setCode('echo "code";');
        $this->setReturnsReference();
        $this->getCode()->shouldReturn("throw new \Prophecy\Exception\Doubler\ReturnByReferenceException('Returning by reference not supported', get_class(\$this), 'getTitle');");
    }

    function its_setCode_provided_with_null_cleans_method_body()
    {
        $this->setCode(null);
        $this->getCode()->shouldReturn('');
    }

    function it_is_constructable_with_code()
    {
        $this->beConstructedWith('getTitle', 'die();');
        $this->getCode()->shouldReturn('die();');
    }

    function it_does_not_have_arguments_by_default()
    {
        $this->getArguments()->shouldHaveCount(0);
    }

    function it_supports_adding_arguments(ArgumentNode $argument1, ArgumentNode $argument2)
    {
        $this->addArgument($argument1);
        $this->addArgument($argument2);

        $this->getArguments()->shouldReturn(array($argument1, $argument2));
    }

    function it_does_not_have_return_type_by_default()
    {
        $this->hasReturnType()->shouldReturn(false);
    }

    function it_setReturnType_sets_return_type()
    {
        $returnType = 'array';

        $this->setReturnType($returnType);

        $this->hasReturnType()->shouldReturn(true);
        $this->getReturnType()->shouldReturn($returnType);
    }

    function it_handles_object_return_type()
    {
        $this->setReturnType('object');
        $this->getReturnType()->shouldReturn(version_compare(PHP_VERSION, '7.2', '>=') ? 'object' : '\object');
    }

    function it_handles_type_aliases()
    {
        $this->setReturnType('double');
        $this->getReturnType()->shouldReturn(version_compare(PHP_VERSION, '7.0', '>=') ? 'float' : '\float');

        $this->setReturnType('real');
        $this->getReturnType()->shouldReturn(version_compare(PHP_VERSION, '7.0', '>=') ? 'float' : '\float');

        $this->setReturnType('boolean');
        $this->getReturnType()->shouldReturn(version_compare(PHP_VERSION, '7.0', '>=') ? 'bool' : '\bool');

        $this->setReturnType('integer');
        $this->getReturnType()->shouldReturn(version_compare(PHP_VERSION, '7.0', '>=') ? 'int' : '\int');
    }

    function it_handles_null_return_type()
    {
        $this->setReturnType(null);
        $this->getReturnType()->shouldReturn(null);
    }
}
