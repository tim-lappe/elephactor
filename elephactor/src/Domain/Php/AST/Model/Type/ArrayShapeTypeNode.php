<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Type;

use TimLappe\Elephactor\Domain\Php\AST\Model\AbstractNode;
use TimLappe\Elephactor\Domain\Php\AST\Model\TypeNode;

final class ArrayShapeTypeNode extends AbstractNode implements TypeNode
{
    /**
     * @param list<ArrayShapeField> $fields
     */
    public function __construct(
        private readonly array $fields
    ) {
        parent::__construct();

        foreach ($fields as $field) {
            $this->children()->add('type', $field->type());
        }
    }

    /**
     * @return list<ArrayShapeField>
     */
    public function fields(): array
    {
        return $this->fields;
    }
}
