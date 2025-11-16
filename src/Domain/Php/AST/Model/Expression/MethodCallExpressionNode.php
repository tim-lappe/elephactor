<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class MethodCallExpressionNode extends AbstractNode implements ExpressionNode
{
    private IdentifierNode|ExpressionNode $method;
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        private readonly ExpressionNode $object,
        Identifier|ExpressionNode $method,
        private readonly array $arguments,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct(NodeKind::METHOD_CALL_EXPRESSION);

        $this->method = $method instanceof Identifier ? new IdentifierNode($method, $this) : $method;
    }

    public function object(): ExpressionNode
    {
        return $this->object;
    }

    public function method(): IdentifierNode|ExpressionNode
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

        $children[] = $this->method;

        return [
            ...$children,
            ...$this->arguments,
        ];
    }
}
