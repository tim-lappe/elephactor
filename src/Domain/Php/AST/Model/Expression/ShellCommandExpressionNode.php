<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;

final class ShellCommandExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ExpressionNode|string> $parts
     */
    public function __construct(
        private readonly array $parts
    ) {
        parent::__construct(NodeKind::SHELL_COMMAND_EXPRESSION);
    }

    /**
     * @return list<ExpressionNode|string>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return array_values(array_filter(
            $this->parts,
            static fn ($part): bool => $part instanceof ExpressionNode,
        ));
    }
}
