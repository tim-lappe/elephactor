<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ArgumentNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final readonly class MethodCallExpressionNode extends AbstractNode implements ExpressionNode
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
        parent::__construct();

        $this->method = $method instanceof Identifier ? new IdentifierNode($method) : $method;

        $this->children()->add($this->object);
        $this->children()->add($this->method);

        foreach ($this->arguments as $argument) {
            $this->children()->add($argument);
        }
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
}
