<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\UseTrait;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\QualifiedNameNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\QualifiedName;

final class TraitPrecedenceAdaptationNode extends AbstractNode implements TraitAdaptationNode
{
    private QualifiedNameNode $originatingTrait;
    private IdentifierNode $method;

    /**
     * @var list<QualifiedNameNode>
     */
    private readonly array $insteadOf;

    /**
     * @param list<QualifiedName> $insteadOf
     */
    public function __construct(
        QualifiedName $originatingTrait,
        Identifier $method,
        array $insteadOf
    ) {
        if ($insteadOf === []) {
            throw new \InvalidArgumentException('Trait precedence adaptation requires at least one replacement trait');
        }

        parent::__construct(NodeKind::TRAIT_PRECEDENCE_ADAPTATION);

        $this->originatingTrait = new QualifiedNameNode($originatingTrait, $this);
        $this->method = new IdentifierNode($method, $this);
        $this->insteadOf = array_map(
            fn (QualifiedName $qualifiedName): QualifiedNameNode => new QualifiedNameNode($qualifiedName, $this),
            $insteadOf,
        );
    }

    public function originatingTrait(): QualifiedNameNode
    {
        return $this->originatingTrait;
    }

    public function method(): IdentifierNode
    {
        return $this->method;
    }

    /**
     * @return list<QualifiedNameNode>
     */
    public function insteadOf(): array
    {
        return $this->insteadOf;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return [
            $this->originatingTrait,
            $this->method,
            ...$this->insteadOf,
        ];
    }
}
