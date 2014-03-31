<?php
namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;

class KeywordPatch implements ClassPatchInterface
{
    public function supports(ClassNode $node)
    {
        return true;
    }

    public function apply(ClassNode $node)
    {
        $methodNames = array_keys($node->getMethods());
        $methodsToRemove = array_intersect($methodNames, $this->getKeywords());
        foreach ($methodsToRemove as $methodName) {
            $node->removeMethod($methodName);
        }
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority() {
        return 50;
    }

    public function getKeywords() {

        return array(
            '__halt_compiler',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'eval',
            'exit',
            'extends',
            'final',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'namespace',
            'new',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor'
        );
    }
}
