<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class StaticCallExpressionNode extends AbstractNode implements ExpressionNode
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
        parent::__construct(NodeKind::STATIC_CALL_EXPRESSION);

        $this->classReference = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference, $this)
            : $classReference;
        $this->method = $method instanceof Identifier
            ? new IdentifierNode($method, $this)
            : $method;
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

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [];

        $children[] = $this->classReference;
        $children[] = $this->method;

        return [
            ...$children,
            ...$this->arguments,
        ];
    }
}
