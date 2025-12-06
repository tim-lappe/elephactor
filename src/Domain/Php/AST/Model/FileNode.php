<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model;

final readonly class FileNode extends AbstractNode
{
    /**
     * @param list<StatementNode> $statements
     */
    public function __construct(
        array $statements
    ) {
        parent::__construct();

        foreach ($statements as $statement) {
            $this->children()->add($statement);
        }
    }

    /**
     * @return NodeCollection
     */
    public function classLikeDeclerations(): NodeCollection
    {
        return $this->children()->filterType(ClassLikeNode::class);
    }
}
