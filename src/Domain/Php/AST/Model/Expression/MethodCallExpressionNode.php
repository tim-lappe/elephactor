<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class MethodCallExpressionNode extends AbstractNode implements ExpressionNode
{
    /**
     * @param list<ArgumentNode> $arguments
     */
    public function __construct(
        ExpressionNode $object,
        Identifier|ExpressionNode $method,
        array $arguments,
        private readonly bool $nullsafe = false
    ) {
        parent::__construct();

        $methodNode = $method instanceof Identifier ? new IdentifierNode($method) : $method;

        $this->children()->add('object', $object);
        $this->children()->add('method', $methodNode);

        foreach ($arguments as $argument) {
            $this->children()->add('argument', $argument);
        }
    }

    public function object(): ExpressionNode
    {
        return $this->children()->getOne('object', ExpressionNode::class) ?? throw new \RuntimeException('Object expression not found');
    }

    public function method(): IdentifierNode|ExpressionNode
    {
        return $this->children()->getOne('method', IdentifierNode::class)
            ?? $this->children()->getOne('method', ExpressionNode::class)
            ?? throw new \RuntimeException('Method not found');
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
        return $this->children()->getAllOf('argument', ArgumentNode::class);
    }
}
