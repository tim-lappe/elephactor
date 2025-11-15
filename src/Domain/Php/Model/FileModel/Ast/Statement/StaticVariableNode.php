<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class StaticVariableNode extends AbstractNode
{
    public function __construct(
        private readonly Identifier $name,
        private readonly ?ExpressionNode $defaultValue = null
    ) {
        parent::__construct(NodeKind::STATIC_VARIABLE);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function defaultValue(): ?ExpressionNode
    {
        return $this->defaultValue;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->defaultValue !== null ? [$this->defaultValue] : [];
    }
}
