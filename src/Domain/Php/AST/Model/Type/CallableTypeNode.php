<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class CallableTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<CallableParameter> $parameters
     */
    public function __construct(
        private readonly array $parameters,
        private readonly ?TypeNode $returnType
    ) {
        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    /**
     * @return list<CallableParameter>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function returnType(): ?TypeNode
    {
        return $this->returnType;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        $children = array_values(array_filter(
            array_map(
                static fn (CallableParameter $parameter): ?TypeNode => $parameter->type(),
                $this->parameters,
            ),
        ));

        if ($this->returnType !== null) {
            $children[] = $this->returnType;
        }

        return $children;
    }
}
