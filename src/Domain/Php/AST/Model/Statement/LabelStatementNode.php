<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;

final class LabelStatementNode extends AbstractNode implements StatementNode
{
    public function __construct(
        private readonly Identifier $label
    ) {
        parent::__construct();
    }

    public function label(): Identifier
    {
        return $this->label;
    }

}
