<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Model\FileModel\Ast\Value;

final class FullyQualifiedName
{
    /**
     * @param list<Identifier> $parts
     */
    public function __construct(
        private readonly array $parts,
    ) {
        if ($parts === []) {
            throw new \InvalidArgumentException('Fully qualified name requires at least one identifier');
        }
    }

    /**
     * @return list<Identifier>
     */
    public function parts(): array
    {
        return $this->parts;
    }

    public function containsName(string $name): bool
    {
        $normalizedName = trim(strtolower($name), '\\');
        if ($this->parts === []) {
            return false;
        }

        $explodedName = explode('\\', $normalizedName);
        $lastPart = array_pop($explodedName);

        $lastPart = new Identifier($lastPart);
        $thisLastPart = $this->parts[count($this->parts) - 1];

        if ($lastPart->equals($thisLastPart)) {
            return true;
        }

        return $this->__toString() === $normalizedName;
    }

    public function equals(FullyQualifiedName $other): bool
    {
        return $this->__toString() === $other->__toString();
    }

    public function __toString(): string
    {
        return implode('\\', array_map(
            static fn (Identifier $identifier): string => $identifier->value(),
            $this->parts,
        ));
    }
}
