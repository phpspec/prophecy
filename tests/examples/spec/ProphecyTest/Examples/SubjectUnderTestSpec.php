<?php
namespace spec\ProphecyTest\Examples;

use PhpSpec\ObjectBehavior;
use ProphecyTest\Examples\CollaboratorWithReturnTypehinting;

class SubjectUnderTestSpec extends ObjectBehavior
{
    function it_calls_something_on_its_dependency(CollaboratorWithReturnTypehinting $collaboratorWithReturnTypehinting)
    {
        $this->getAnIntFromThisThing($collaboratorWithReturnTypehinting);
        $collaboratorWithReturnTypehinting->getAnInt()->shouldHaveBeenCalled();
    }
}
