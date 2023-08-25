<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator\Node;

use Prophecy\Exception\Doubler\MethodNotExtendableException;
use Prophecy\Exception\InvalidArgumentException;

/**
 * Class node.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassNode
{
    /**
     * @var string
     */
    private $parentClass = 'stdClass';
    /**
     * @var list<string>
     */
    private $interfaces  = array();

    /**
     * @var array<string, string>
     *
     * @phpstan-var array<string, 'public'|'private'|'protected'>
     */
    private $properties  = array();

    /**
     * @var list<string>
     */
    private $unextendableMethods = array();

    /**
     * @var bool
     */
    private $readOnly = false;

    /**
     * @var array<string, MethodNode>
     */
    private $methods = array();

    /**
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @param string $class
     *
     * @return void
     */
    public function setParentClass($class)
    {
        $this->parentClass = $class ?: 'stdClass';
    }

    /**
     * @return list<string>
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param string $interface
     *
     * @return void
     */
    public function addInterface($interface)
    {
        if ($this->hasInterface($interface)) {
            return;
        }

        array_unshift($this->interfaces, $interface);
    }

    /**
     * @param string $interface
     *
     * @return bool
     */
    public function hasInterface($interface)
    {
        return in_array($interface, $this->interfaces);
    }

    /**
     * @return array<string, string>
     *
     * @phpstan-return array<string, 'public'|'private'|'protected'>
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $name
     * @param string $visibility
     *
     * @return void
     *
     * @phpstan-param 'public'|'private'|'protected' $visibility
     */
    public function addProperty($name, $visibility = 'public')
    {
        $visibility = strtolower($visibility);

        if (!\in_array($visibility, array('public', 'private', 'protected'), true)) {
            throw new InvalidArgumentException(sprintf(
                '`%s` property visibility is not supported.', $visibility
            ));
        }

        $this->properties[$name] = $visibility;
    }

    /**
     * @return array<string, MethodNode>
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param MethodNode $method
     * @param bool       $force
     *
     * @return void
     */
    public function addMethod(MethodNode $method, $force = false)
    {
        if (!$this->isExtendable($method->getName())){
            $message = sprintf(
                'Method `%s` is not extendable, so can not be added.', $method->getName()
            );
            throw new MethodNotExtendableException($message, $this->getParentClass(), $method->getName());
        }

        if ($force || !isset($this->methods[$method->getName()])) {
            $this->methods[$method->getName()] = $method;
        }
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function removeMethod($name)
    {
        unset($this->methods[$name]);
    }

    /**
     * @param string $name
     *
     * @return MethodNode|null
     */
    public function getMethod($name)
    {
        return $this->hasMethod($name) ? $this->methods[$name] : null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMethod($name)
    {
        return isset($this->methods[$name]);
    }

    /**
     * @return list<string>
     */
    public function getUnextendableMethods()
    {
        return $this->unextendableMethods;
    }

    /**
     * @param string $unextendableMethod
     *
     * @return void
     */
    public function addUnextendableMethod($unextendableMethod)
    {
        if (!$this->isExtendable($unextendableMethod)){
            return;
        }
        $this->unextendableMethods[] = $unextendableMethod;
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isExtendable($method)
    {
        return !in_array($method, $this->unextendableMethods);
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     *
     * @return void
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }
}
