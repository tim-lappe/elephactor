<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class ConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    private QualifiedNameNode $name;

    public function __construct(
        QualifiedName $name
    ) {
        parent::__construct(NodeKind::CONSTANT_FETCH_EXPRESSION);

        $this->name = new QualifiedNameNode($name, $this);
    }

    public function name(): QualifiedNameNode
    {
        return $this->name;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [$this->name];
    }
}
