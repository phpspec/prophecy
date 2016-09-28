<?php

namespace Fixtures\Prophecy;

use I\Simply;

class OptionalDepsClass
{
    public function iHaveAStrangeTypeHintedArg(\I\Simply\Am\Nonexistent $class)
    {
    }

    public function iHaveAnEvenStrangerTypeHintedArg(Simply\Am\Not $class)
    {
    }
}
