<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class NamespaceDefinitionNode extends AbstractNode implements StatementNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        QualifiedName $name,
        array $statements,
        private readonly bool $bracketed = false
    ) {
        parent::__construct();

        $this->children()->add('name', new QualifiedNameNode($name));

        foreach ($statements as $statement) {
            $this->children()->add('statement', $statement);
        }
    }

    public function name(): QualifiedNameNode
    {
        return $this->children()->getOne('name', QualifiedNameNode::class) ?? throw new \RuntimeException('Namespace name not found');
    }

    public function isBracketed(): bool
    {
        return $this->bracketed;
    }

    /**
     * @return list<StatementNode>
     */
    public function statements(): array
    {
        return $this->children()->getAllOf('statement', StatementNode::class);
    }
}
