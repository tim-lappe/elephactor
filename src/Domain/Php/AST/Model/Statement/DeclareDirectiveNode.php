<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class DeclareDirectiveNode extends AbstractNode
{
    public function __construct(
        private readonly Identifier $name,
        private readonly ExpressionNode $value
    ) {
        parent::__construct(NodeKind::DECLARE_DIRECTIVE);
    }

    public function name(): Identifier
    {
        return $this->name;
    }

    public function value(): ExpressionNode
    {
        return $this->value;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->value];
    }
}
