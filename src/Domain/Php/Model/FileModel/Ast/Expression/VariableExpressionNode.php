<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class VariableExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        private readonly Identifier|ExpressionNode $name
    ) {
        parent::__construct(NodeKind::VARIABLE_EXPRESSION);
    }

    public function name(): Identifier|ExpressionNode
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return $this->name instanceof ExpressionNode ? [$this->name] : [];
    }
}
