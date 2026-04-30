<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Expression;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\ExpressionNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class ConstantFetchExpressionNode extends AbstractNode implements ExpressionNode
{
    public function __construct(
        QualifiedName $name
    ) {
        parent::__construct();

        $this->children()->add('name', new QualifiedNameNode($name));
    }

    public function name(): QualifiedNameNode
    {
        return $this->children()->getOne('name', QualifiedNameNode::class) ?? throw new \RuntimeException('Name not found');
    }
}
