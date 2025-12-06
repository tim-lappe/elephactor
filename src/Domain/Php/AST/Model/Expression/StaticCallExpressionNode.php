<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final readonly class StaticCallExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode|ExpressionNode $classReference;
    private IdentifierNode|ExpressionNode $method;
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier|ExpressionNode $method,
        private readonly array $arguments
    ) {
        parent::__construct();

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $this->method = $method instanceof Identifier
            ? new IdentifierNode($method)
            : $method;

        $this->children()->add($this->classReference);
        $this->children()->add($this->method);

        foreach ($this->arguments as $argument) {
            $this->children()->add($argument);
        }
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->classReference;
    }

    public function method(): IdentifierNode|ExpressionNode
    {
        return $this->method;
    }

    /**
     * @return list<ArgumentNode>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
