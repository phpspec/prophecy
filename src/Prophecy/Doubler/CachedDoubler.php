<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler;

use ReflectionClass;

/**
 * Cached class doubler.
 * Prevents mirroring/creation of the same structure twice.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CachedDoubler extends Doubler
{
    /**
     * @var array<string, class-string>
     */
    private static $classes = array();

    protected function createDoubleClass(?ReflectionClass $class, array $interfaces)
    {
        $classId = $this->generateClassId($class, $interfaces);
        if (isset(self::$classes[$classId])) {
            return self::$classes[$classId];
        }

        return self::$classes[$classId] = parent::createDoubleClass($class, $interfaces);
    }

    /**
     * @param ReflectionClass<object>|null $class
     * @param ReflectionClass<object>[]    $interfaces
     *
     * @return string
     */
    private function generateClassId(?ReflectionClass $class, array $interfaces)
    {
        $parts = array();
        if (null !== $class) {
            $parts[] = $class->getName();
        }
        foreach ($interfaces as $interface) {
            $parts[] = $interface->getName();
        }
        foreach ($this->getClassPatches() as $patch) {
            $parts[] = get_class($patch);
        }
        sort($parts);

        return md5(implode('', $parts));
    }

    /**
     * @return void
     */
    public function resetCache()
    {
        self::$classes = array();
    }
}
