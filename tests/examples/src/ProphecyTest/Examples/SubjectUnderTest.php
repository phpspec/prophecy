<?php
namespace ProphecyTest\Examples;

class SubjectUnderTest
{
    public function getAnIntFromThisThing(CollaboratorWithReturnTypehinting $dependencyWithReturnTypehinting)
    {
        $int = $dependencyWithReturnTypehinting->getAnInt();
    }
}
