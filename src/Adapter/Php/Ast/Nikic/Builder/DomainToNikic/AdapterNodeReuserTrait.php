<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\DomainToNikic;

use PhpParser\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;

trait AdapterNodeReuserTrait
{
    /**
     * @template T of Node
     * @param Ast\Node $domainNode
     * @param T        $built
     * @return T
     */
    private function reuseAdapterNode(Ast\Node $domainNode, Node $built)
    {
        $adapter = $domainNode->getAdapterNode();

        if ($adapter instanceof Node && $adapter::class === $built::class) {
            $attributes = $adapter->getAttributes();
            foreach ($built->getAttributes() as $key => $value) {
                $attributes[$key] = $value;
            }

            $built->setAttributes($attributes);
        }

        /** @var T $built */
        return $built;
    }
}
