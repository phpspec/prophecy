<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator;

/**
 * Class code creator.
 * Generates PHP code for specific class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassCodeGenerator
{
    /**
     * Generates PHP code for class node.
     *
     * @param string         $classname
     * @param Node\ClassNode $class
     *
     * @return string
     */
    public function generate($classname, Node\ClassNode $class)
    {
        $parts     = explode('\\', $classname);
        $classname = array_pop($parts);
        $namespace = implode('\\', $parts);

        $code = sprintf("class %s extends \%s implements %s {\n",
            $classname, $class->getParentClass(), implode(', ',
                array_map(function ($interface) {return '\\'.$interface;}, $class->getInterfaces())
            )
        );

        foreach ($class->getProperties() as $name => $visibility) {
            $code .= sprintf("%s \$%s;\n", $visibility, $name);
        }
        $code .= "\n";

        foreach ($class->getMethods() as $method) {
            $code .= $this->generateMethod($method)."\n";
        }
        $code .= "\n}";

        return sprintf("namespace %s {\n%s\n}", $namespace, $code);
    }

    private function generateMethod(Node\MethodNode $method)
    {
        $php = sprintf("%s %s function %s%s(%s) {\n",
            $method->getVisibility(),
            $method->isStatic() ? 'static' : '',
            $method->returnsReference() ? '&':'',
            $method->getName(),
            $this->generateArguments($method)
        );
        $php .= $method->getCode()."\n";

        return $php.'}';
    }

    private function generateArguments(Node\MethodNode $method)
    {
        return implode(', ', array_map(
            array($this, 'generateArgument'),
            $method->getArguments()
        ));
    }

    private function generateArgument(Node\ArgumentNode $argument)
    {
        return $this->generateArgumentTypeHint($argument)
              .$this->generateArgumentPrefix($argument)
              .'$'.$argument->getName()
              .$this->generateArgumentDefault($argument);
    }

    private function generateArgumentTypeHint(Node\ArgumentNode $argument)
    {
        if (!($hint = $argument->getTypeHint())) {
            return '';
        }

        if ('array' === $hint || 'callable' === $hint) {
            return $hint;
        }

        return '\\'.$hint;
    }

    private function generateArgumentPrefix(Node\ArgumentNode $argument)
    {
        $prefix = ' ';

        if ($argument->isPassedByReference()) {
            $prefix .= '&';
        }

        if ($argument->isVariadic()) {
            $prefix .= '...';
        }

        return $prefix;
    }

    private function generateArgumentDefault(Node\ArgumentNode $argument)
    {
        if (!$argument->hasDefault()) {
            return '';
        }

        return ' = '.var_export($argument->getDefault(), true);
    }
}
