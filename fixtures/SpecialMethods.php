<?php

namespace Fixtures\Prophecy;

class SpecialMethods
{
    public function __construct()
    {
    }

    function __destruct()
    {
    }

    function __call($name, $arguments)
    {
    }

    function __sleep()
    {
    }

    function __wakeup()
    {
    }

    function __toString()
    {
        return '';
    }

    function __invoke()
    {
    }

    function __soapCall($function_name, array $arguments, array $options = null, $input_headers = null, array &$output_headers = null)
    {
    }

}
