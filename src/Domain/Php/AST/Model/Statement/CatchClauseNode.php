<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
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
        array $types,
        private readonly Identifier $variable,
        array $statements
    ) {
        if ($types === []) {
            throw new \InvalidArgumentException('Catch clause requires at least one type');
        }

        parent::__construct();

        foreach ($types as $type) {
            $this->children()->add('type', $type);
        }

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    /**
     * @return list<TypeNode>
     */
    public function types(): array
    {
        return $this->children()->getAllOf('type', TypeNode::class);
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
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
