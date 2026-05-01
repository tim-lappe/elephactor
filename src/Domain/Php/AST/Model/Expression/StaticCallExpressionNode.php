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

final class StaticCallExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        QualifiedName|ExpressionNode $classReference,
        Identifier|ExpressionNode $method,
        array $arguments
    ) {
        parent::__construct();

        $classReferenceNode = $classReference instanceof QualifiedName
            ? new QualifiedNameNode($classReference)
            : $classReference;
        $methodNode = $method instanceof Identifier
            ? new IdentifierNode($method)
            : $method;

        $this->children()->add('classReference', $classReferenceNode);
        $this->children()->add('method', $methodNode);

        foreach ($arguments as $argument) {
            $this->children()->add('argument', $argument);
        }
    }

    public function classReference(): QualifiedNameNode|ExpressionNode
    {
        return $this->children()->getOne('classReference', QualifiedNameNode::class)
            ?? $this->children()->getOne('classReference', ExpressionNode::class)
            ?? throw new \RuntimeException('Class reference not found');
    }

    public function method(): IdentifierNode|ExpressionNode
    {
        return $this->children()->getOne('method', IdentifierNode::class)
            ?? $this->children()->getOne('method', ExpressionNode::class)
            ?? throw new \RuntimeException('Method not found');
    }

    /**
     * @return list<ArgumentNode>
     */
    public function arguments(): array
    {
        return $this->children()->getAllOf('argument', ArgumentNode::class);
    }
}
