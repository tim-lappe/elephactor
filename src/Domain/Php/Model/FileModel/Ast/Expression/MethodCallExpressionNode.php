<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Expression;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class MethodCallExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        private readonly ExpressionNode $object,
        private readonly Identifier|ExpressionNode $method,
        private readonly array $arguments,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct(NodeKind::METHOD_CALL_EXPRESSION);
    }

    public function object(): ExpressionNode
    {
        return $this->object;
    }

    public function method(): Identifier|ExpressionNode
    {
        return $this->method;
    }

    public function isNullsafe(): bool
    {
        return $this->nullsafe;
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
        $children = [$this->object];

        if ($this->method instanceof ExpressionNode) {
            $children[] = $this->method;
        }

        return [
            ...$children,
            ...$this->arguments,
        ];
    }
}
