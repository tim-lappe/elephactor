<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\MethodModifiers;

final class MethodDeclarationNode extends AbstractNode implements MemberNode
{
    private IdentifierNode $name;
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<ParameterNode>      $parameters
     * @param list<StatementNode>      $bodyStatements
     */
    public function __construct(
        Identifier $name,
        private readonly MethodModifiers $modifiers,
        private readonly array $attributes,
        private readonly array $parameters,
        private readonly array $bodyStatements,
        private readonly ?TypeNode $returnType = null,
        private readonly bool $returnsByReference = false,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct(NodeKind::METHOD_DECLARATION);

        $this->name = new IdentifierNode($name, $this);
    }

    public function name(): IdentifierNode
    {
        return $this->name;
    }

    public function modifiers(): MethodModifiers
    {
        return $this->modifiers;
    }

    /**
     * @return list<AttributeGroupNode>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return list<ParameterNode>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return list<StatementNode>
     */
    public function bodyStatements(): array
    {
        return $this->bodyStatements;
    }

    public function returnType(): ?TypeNode
    {
        return $this->returnType;
    }

    public function returnsByReference(): bool
    {
        return $this->returnsByReference;
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = [
            $this->name,
            ...$this->attributes,
            ...$this->parameters,
            ...$this->bodyStatements,
        ];

        if ($this->returnType !== null) {
            $children[] = $this->returnType;
        }

        return $children;
    }
}
