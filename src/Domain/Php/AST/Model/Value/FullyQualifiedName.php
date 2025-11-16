<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\AST\Model\Value;

final readonly class FullyQualifiedName extends QualifiedName
{
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

    public function removeLastPart(): FullyQualifiedName
    {
        return new FullyQualifiedName(array_slice($this->parts, 0, -1));
    }

    public function extend(Identifier $identifier): FullyQualifiedName
    {
        return new FullyQualifiedName([...$this->parts, $identifier]);
    }

    public function changeLastPart(Identifier $identifier): FullyQualifiedName
    {
        $count = count($this->parts);
        $parts = $this->parts;
        $parts[$count - 1] = $identifier;
        $parts = array_values($parts);

        return new FullyQualifiedName($parts);
    }

    public function __toString(): string
    {
        return '\\' . implode('\\', array_map(
            static fn (Identifier $identifier): string => $identifier->value(),
            $this->parts,
        ));
    }
}
