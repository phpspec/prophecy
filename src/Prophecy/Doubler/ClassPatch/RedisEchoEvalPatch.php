<?php
namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;

class RedisEchoEvalPatch implements ClassPatchInterface
{
    public function supports(ClassNode $node)
    {
        return 'Redis' === $node->getParentClass();
    }

    public function apply(ClassNode $node)
    {
        $node->removeMethod('echo');
        $node->removeMethod('eval');
    }

    /**
     * Returns patch priority, which determines when patch will be applied.
     *
     * @return int Priority number (higher - earlier)
     */
    public function getPriority() {
        return 50;
    }
}
