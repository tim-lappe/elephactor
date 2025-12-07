<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Transformer;

use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Traversal\VisitorContext;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseStatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Statement\UseClauseNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Transformer\Refactorer\QualifiedNameChanger;

final class RenameImportTransformer extends AbsractNodeTransformer
{
    public function __construct(
        private readonly QualifiedName $oldFullyQualifiedName,
        private readonly QualifiedName $newFullyQualifiedName,
    ) {
        parent::__construct();
    }

    public function enter(Node $node, VisitorContext $context): void
    {
        if ($node instanceof UseStatementNode) {
            $clauses = $node->clauses();
            $groupPrefix = $node->groupPrefix();
            if ($groupPrefix !== null) {
                $this->handleGroupImport($node, $groupPrefix->qualifiedName(), $clauses);
            } else {
                $this->handleSimpleImport($clauses);
            }
        }
    }

    public function leave(Node $node, VisitorContext $context): void
    {
    }

    /**
     * @param list<UseClauseNode> $clauses
     */
    private function handleSimpleImport(array $clauses): void
    {
        foreach ($clauses as $clause) {
            if ($clause->name()->qualifiedName()->equals($this->oldFullyQualifiedName)) {
                $replacementQualifiedName = new QualifiedName($this->newFullyQualifiedName->parts());
                $this->refactorings->add(new QualifiedNameChanger($clause->name(), $replacementQualifiedName));
            }
        }
    }

    /**
     * @param list<UseClauseNode> $clauses
     */
    private function handleGroupImport(UseStatementNode $useStatementNode, QualifiedName $groupPrefix, array $clauses): void
    {
        $groupParts = $groupPrefix->parts();
        $originals = $this->originalClauseParts($groupParts, $clauses);
        $match = $this->matchingClause($clauses, $originals);

        if ($match === null) {
            return;
        }

        [$newGroupParts, $newParts] = $this->splitNewParts($groupParts, $this->newFullyQualifiedName->parts());
        $this->updateGroupPrefix($useStatementNode, $groupParts, $newGroupParts);
        $this->rewriteClauses($clauses, $originals, $match, $newParts, $newGroupParts);
    }

    /**
     * @param  list<UseClauseNode>          $clauses
     * @param  list<Identifier>             $groupParts
     * @return array<int, list<Identifier>>
     */
    private function originalClauseParts(array $groupParts, array $clauses): array
    {
        $originals = [];
        foreach ($clauses as $clause) {
            $originals[spl_object_id($clause)] = [...$groupParts, ...$clause->name()->qualifiedName()->parts()];
        }
        return $originals;
    }

    /**
     * @param list<UseClauseNode>          $clauses
     * @param array<int, list<Identifier>> $originals
     */
    private function matchingClause(array $clauses, array $originals): ?UseClauseNode
    {
        foreach ($clauses as $clause) {
            $combinedQualifiedName = new QualifiedName($originals[spl_object_id($clause)]);
            if ($combinedQualifiedName->equals($this->oldFullyQualifiedName)) {
                return $clause;
            }
        }

        return null;
    }

    /**
     * @param  list<Identifier>                                $groupParts
     * @param  list<Identifier>                                $newParts
     * @return array{0: list<Identifier>, 1: list<Identifier>}
     */
    private function splitNewParts(array $groupParts, array $newParts): array
    {
        $commonPrefixLength = 0;
        for ($i = 0; $i < min(count($groupParts), count($newParts)); $i++) {
            if (!$groupParts[$i]->equals($newParts[$i])) {
                break;
            }
            $commonPrefixLength++;
        }

        $newGroupParts = array_slice($newParts, 0, max(1, $commonPrefixLength));

        return [$newGroupParts, $newParts];
    }

    /**
     * @param list<Identifier> $groupParts
     * @param list<Identifier> $newGroupParts
     */
    private function updateGroupPrefix(UseStatementNode $useStatementNode, array $groupParts, array $newGroupParts): void
    {
        $groupPrefixNode = $useStatementNode->groupPrefix();
        if ($groupPrefixNode !== null && count($newGroupParts) !== count($groupParts)) {
            $this->refactorings->add(new QualifiedNameChanger($groupPrefixNode, new QualifiedName($newGroupParts)));
        }
    }

    /**
     * @param list<UseClauseNode>          $clauses
     * @param array<int, list<Identifier>> $originals
     * @param list<Identifier>             $newParts
     * @param list<Identifier>             $newGroupParts
     */
    private function rewriteClauses(
        array $clauses,
        array $originals,
        UseClauseNode $matched,
        array $newParts,
        array $newGroupParts,
    ): void {
        foreach ($clauses as $clause) {
            $desiredParts = $clause === $matched ? $newParts : $originals[spl_object_id($clause)];

            $newClauseParts = array_slice($desiredParts, count($newGroupParts));
            if ($newClauseParts === []) {
                $newClauseParts = [$desiredParts[count($desiredParts) - 1]];
            }

            $this->refactorings->add(new QualifiedNameChanger($clause->name(), new QualifiedName($newClauseParts)));
        }
    }
}
