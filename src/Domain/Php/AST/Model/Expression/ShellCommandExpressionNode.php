<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;

final class ShellCommandExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ExpressionNode|string> $parts
     */
    public function __construct(
        array $parts
    ) {
        parent::__construct();

        foreach ($parts as $part) {
            if ($part instanceof ExpressionNode) {
                $this->children()->add('part', $part);
            }
        }
    }

    /**
     * @return list<ExpressionNode|string>
     */
    public function parts(): array
    {
        return $this->children()->getAllOf('part', ExpressionNode::class);
    }
}
