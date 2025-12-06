<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class NewExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $classReference;
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        private readonly array $arguments
    ) {
        parent::__construct();

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;

        $this->children()->add($this->classReference);

        foreach ($this->arguments as $argument) {
            $this->children()->add($argument);
        }
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->classReference;
    }

    /**
     * @return list<ArgumentNode>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
