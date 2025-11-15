<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class ClosureUseVariableNode extends AbstractNode
{
    public function __construct(
        private readonly Identifier $name,
        private readonly bool $byReference = false
    ) {
        parent::__construct(NodeKind::CLOSURE_USE_VARIABLE);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function byReference(): bool
    {
        return $this->byReference;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
