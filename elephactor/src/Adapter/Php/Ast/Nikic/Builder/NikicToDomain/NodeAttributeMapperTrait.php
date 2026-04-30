<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Adapter\Php\Ast\Nikic\Builder\NikicToDomain;

use PhpParser\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model as Ast;

trait NodeAttributeMapperTrait
{
    /**
     * @template T of Ast\Node
     * @param T $target
     * @return T
     */
    private function applyAttributes(Node $source, Ast\Node $target)
    {
        $target->setAdapterNode($source);

        return $target;
    }
}
