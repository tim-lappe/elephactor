<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\Node;
use TimLappe\Elephactor\Domain\Php\AST\Model\NodeKind;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class ArrayShapeTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<ArrayShapeField> $fields
     */
    public function __construct(
        private readonly array $fields
    ) {
        parent::__construct(NodeKind::TYPE_REFERENCE);
    }

    /**
     * @return list<ArrayShapeField>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @return list<Node>
     */
    public function children(): array
    {
        return array_map(
            static fn (ArrayShapeField $field): TypeNode => $field->type(),
            $this->fields,
        );
    }
}
