<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\QualifiedName;

final class StaticCallExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        private readonly QualifiedName|ExpressionNode $classReference,
        private readonly Identifier|ExpressionNode $method,
        private readonly array $arguments
    ) {
        parent::__construct(NodeKind::STATIC_CALL_EXPRESSION);
    }

    public function classReference(): QualifiedName|ExpressionNode
    {
        return $this->classReference;
    }

    public function method(): Identifier|ExpressionNode
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

        if ($this->classReference instanceof ExpressionNode) {
            $children[] = $this->classReference;
        }

        if ($this->method instanceof ExpressionNode) {
            $children[] = $this->method;
        }

        return [
            ...$children,
            ...$this->arguments,
        ];
    }
}
