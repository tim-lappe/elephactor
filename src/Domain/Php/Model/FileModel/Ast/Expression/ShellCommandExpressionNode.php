<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;

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
