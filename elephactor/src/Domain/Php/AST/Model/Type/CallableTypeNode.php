<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
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
        parent::__construct();

        foreach ($parameters as $parameter) {
            $type = $parameter->type();
            if ($type !== null) {
                $this->children()->add('parameterType', $type);
            }
        }

        if ($this->returnType !== null) {
            $this->children()->add('returnType', $this->returnType);
        }
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
        return $this->children()->getOne('returnType', TypeNode::class);
    }
}
