<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class CatchClauseNode extends AbstractNode
{
    /**
     * @param list<TypeNode>      $types
     * @param list<StatementNode> $statements
     */
    public function __construct(
        private readonly array $types,
        private readonly Identifier $variable,
        private readonly array $statements
    ) {
        if ($types === []) {
            throw new \InvalidArgumentException('Catch clause requires at least one type');
        }

        parent::__construct(NodeKind::CATCH_CLAUSE);
    }

    /**
     * @return list<TypeNode>
     */
    public function types(): array
    {
        return $this->types;
    }

    public function variable(): Identifier
    {
        return $this->variable;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->statements;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            ...$this->types,
            ...$this->statements,
        ];
    }
}
