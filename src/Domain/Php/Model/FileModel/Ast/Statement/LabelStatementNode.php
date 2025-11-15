<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Statement;

use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\AbstractNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Node;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\NodeKind;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\StatementNode;
use TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value\Identifier;

final class LabelStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly Identifier $label
    ) {
        parent::__construct(NodeKind::LABEL_STATEMENT);
    }

    public function label(): Identifier
    {
        return $this->label;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [];
    }
}
