<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Statement;

final class UseStatementNodeCollection
{
    /**
     * @param list<UseStatementNode> $useStatementNodes
     */
    public function __construct(
        private array $useStatementNodes = [],
    ) {
    }

    public function add(UseStatementNode $useStatementNode): void
    {
        $this->useStatementNodes[] = $useStatementNode;
    }

    public function addAll(UseStatementNodeCollection $useStatementNodeCollection): void
    {
        $this->useStatementNodes = array_merge($this->useStatementNodes, $useStatementNodeCollection->toArray());
    }

    public function filterKind(UseKind $kind): UseStatementNodeCollection
    {
        return new UseStatementNodeCollection(
            array_values(array_filter(
                $this->useStatementNodes,
                fn (UseStatementNode $useStatementNode) => $useStatementNode->useKind() === $kind,
            )),
        );
    }

    /**
     * @return list<UseStatementNode>
     */
    public function toArray(): array
    {
        return $this->useStatementNodes;
    }

    public function count(): int
    {
        return count($this->useStatementNodes);
    }
}
