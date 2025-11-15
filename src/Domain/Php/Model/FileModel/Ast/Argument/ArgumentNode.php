<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class ArgumentNode extends AbstractNode
{
    public function __construct(
        private readonly ExpressionNode $expression,
        private readonly ?Identifier $name = null,
        private readonly bool $unpacked = false
    ) {
        parent::__construct(NodeKind::ARGUMENT);
    }

    public function expression(): ExpressionNode
    {
        return $this->expression;
    }

    public function name(): ?Identifier
    {
        return $this->name;
    }

    public function isUnpacked(): bool
    {
        return $this->unpacked;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->expression];
    }
}
