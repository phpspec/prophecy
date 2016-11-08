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

}
