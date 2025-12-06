<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Declaration;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Argument\ParameterNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Attribute\AttributeGroupNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\MemberNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Name\IdentifierNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\StatementNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\DocBlock;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\Identifier;
use TimLappe\Elephactor\Domain\Php\AST\Model\Value\MethodModifiers;

final readonly class MethodDeclarationNode extends AbstractNode implements MemberNode
{
    /**
     * @param list<AttributeGroupNode> $attributes
     * @param list<ParameterNode>      $parameters
     * @param list<StatementNode>      $bodyStatements
     */
    public function __construct(
        Identifier $name,
        private readonly MethodModifiers $modifiers,
        array $attributes,
        array $parameters,
        array $bodyStatements,
        ?TypeNode $returnType = null,
        private bool $returnsByReference = false,
        private readonly ?DocBlock $docBlock = null
    ) {
        parent::__construct();

        $name = new IdentifierNode($name);

        $this->children()->add("name", $name);

        foreach ($attributes as $attribute) {
            $this->children()->add("attribute", $attribute);
        }

        foreach ($parameters as $parameter) {
            $this->children()->add("parameter", $parameter);
        }

        foreach ($bodyStatements as $bodyStatement) {
            $this->children()->add("bodyStatement", $bodyStatement);
        }

        if ($returnType !== null) {
            $this->children()->add("returnType", $returnType);
        }
    }

    public function name(): IdentifierNode
    {
        return $this->children()->getOne("name", IdentifierNode::class) ?? throw new \RuntimeException('Name not found');
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
        return $this->children()->getAllOf("attribute", AttributeGroupNode::class);
    }

    /**
     * @return list<ParameterNode>
     */
    public function parameters(): array
    {
        return $this->children()->getAllOf("parameter", ParameterNode::class);
    }

    /**
     * @return list<StatementNode>
     */
    public function bodyStatements(): array
    {
        return $this->children()->getAllOf("bodyStatement", StatementNode::class);
    }

    public function returnType(): ?TypeNode
    {
        return $this->children()->getOne("returnType", TypeNode::class);
    }

    public function returnsByReference(): bool
    {
        return $this->returnsByReference;
    }

    public function docBlock(): ?DocBlock
    {
        return $this->docBlock;
    }
}
