<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;

/**
 * Remove method functionality from the double which will clash with php keywords.
 *
 * @author Milan Magudia <milan@magudia.com>
 */
class KeywordPatch implements ClassPatchInterface
{
    /**
     * Support any class
     *
     * @param ClassNode $node
     *
     * @return boolean
     */
    public function supports(ClassNode $node)
    {
        return true;
    }

    /**
     * Remove methods that clash with php keywords
     *
     * @param ClassNode $node
     */
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
        return 49;
    }

    /**
     * Returns array of php keywords.
     *
     * @return array
     */
    private function getKeywords() {
        $keywords = array(
            '__halt_compiler'
        );

        /*
         * Starting from PHP 7.0, almost keywords can be used
         * So we exclude from list of keywords, and only add
         * when PHP version < 7.0
         *
         * https://wiki.php.net/rfc/context_sensitive_lexer
         */
        if(version_compare(PHP_VERSION, '7.0', '<'))
        {
            $keywords[] = 'abstract';
            $keywords[] = 'and';
            $keywords[] = 'array';
            $keywords[] = 'as';
            $keywords[] = 'break';
            $keywords[] = 'callable';
            $keywords[] = 'case';
            $keywords[] = 'catch';
            $keywords[] = 'class';
            $keywords[] = 'clone';
            $keywords[] = 'const';
            $keywords[] = 'continue';
            $keywords[] = 'declare';
            $keywords[] = 'default';
            $keywords[] = 'die';
            $keywords[] = 'do';
            $keywords[] = 'echo';
            $keywords[] = 'else';
            $keywords[] = 'elseif';
            $keywords[] = 'empty';
            $keywords[] = 'enddeclare';
            $keywords[] = 'endfor';
            $keywords[] = 'endforeach';
            $keywords[] = 'endif';
            $keywords[] = 'endswitch';
            $keywords[] = 'endwhile';
            $keywords[] = 'eval';
            $keywords[] = 'exit';
            $keywords[] = 'extends';
            $keywords[] = 'final';
            $keywords[] = 'finally';
            $keywords[] = 'for';
            $keywords[] = 'foreach';
            $keywords[] = 'function';
            $keywords[] = 'global';
            $keywords[] = 'goto';
            $keywords[] = 'if';
            $keywords[] = 'implements';
            $keywords[] = 'include';
            $keywords[] = 'include_once';
            $keywords[] = 'instanceof';
            $keywords[] = 'insteadof';
            $keywords[] = 'interface';
            $keywords[] = 'isset';
            $keywords[] = 'list';
            $keywords[] = 'namespace';
            $keywords[] = 'new';
            $keywords[] = 'or';
            $keywords[] = 'print';
            $keywords[] = 'private';
            $keywords[] = 'protected';
            $keywords[] = 'public';
            $keywords[] = 'require';
            $keywords[] = 'require_once';
            $keywords[] = 'return';
            $keywords[] = 'static';
            $keywords[] = 'switch';
            $keywords[] = 'throw';
            $keywords[] = 'trait';
            $keywords[] = 'try';
            $keywords[] = 'unset';
            $keywords[] = 'use';
            $keywords[] = 'var';
            $keywords[] = 'while';
            $keywords[] = 'xor';
            $keywords[] = 'yield';
        }

        return $keywords;
    }
}
