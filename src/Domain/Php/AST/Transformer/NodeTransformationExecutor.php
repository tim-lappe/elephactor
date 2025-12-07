<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\RefactoringResult;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;

final class NodeTransformationExecutor
{
    /**
     * @param list<AbsractNodeTransformer> $nodeTransformers
     */
    public function __construct(
        private array $nodeTransformers = [],
    ) {
    }

    public function addTransformer(AbsractNodeTransformer $nodeTransformer): void
    {
        $this->nodeTransformers[] = $nodeTransformer;
    }

    public function apply(Node $node): RefactoringResult
    {
        $context = new VisitorContext();
        $this->collect($node, $context);

        $refactoringResult = new RefactoringResult();
        foreach ($this->nodeTransformers as $nodeTransformer) {
            $result = $nodeTransformer->apply();
            $refactoringResult->merge($result);
        }

        return $refactoringResult;
    }

    public function collect(Node $node, VisitorContext $context): void
    {
        foreach ($this->nodeTransformers as $nodeTransformer) {
            $nodeTransformer->enter($node, $context);
        }

        foreach ($node->children()->toArray() as $child) {
            $this->collect($child, $context);
        }

        foreach ($this->nodeTransformers as $nodeTransformer) {
            $nodeTransformer->leave($node, $context);
        }
    }
}
