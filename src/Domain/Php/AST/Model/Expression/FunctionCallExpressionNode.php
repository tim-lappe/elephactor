<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class FunctionCallExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $callable;
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName|ExpressionNode $callable,
        private readonly array $arguments
    ) {
        parent::__construct();

        $this->callable = $callable instanceof QualifiedName
            ? new QualifiedNameNode($callable)
            : $callable;

        $this->children()->add($this->callable);

        foreach ($this->arguments as $argument) {
            $this->children()->add($argument);
        }
    }

    public function callable(): QualifiedNameNode|ExpressionNode
    {
        return $this->callable;
    }

    /**
     * @return list<ArgumentNode>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
