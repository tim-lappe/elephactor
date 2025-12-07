<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class FunctionCallExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName|ExpressionNode $callable,
        array $arguments
    ) {
        parent::__construct();

        $callableNode = $callable instanceof QualifiedName
            ? new QualifiedNameNode($callable)
            : $callable;

        $this->children()->add('callable', $callableNode);

        foreach ($arguments as $argument) {
            $this->children()->add('argument', $argument);
        }
    }

    public function callable(): QualifiedNameNode|ExpressionNode
    {
        return $this->children()->getOne('callable', QualifiedNameNode::class)
            ?? $this->children()->getOne('callable', ExpressionNode::class)
            ?? throw new \RuntimeException('Callable not found');
    }

    /**
     * @return list<ArgumentNode>
     */
    public function arguments(): array
    {
        return $this->children()->getAllOf('argument', ArgumentNode::class);
    }
}
