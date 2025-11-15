<?php

declare (strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Refactoring;

final class ChainedRefactoringExecutor implements RefactoringExecutor
{
    /**
     * @param array<RefactoringExecutor> $executors
     */
    public function __construct(private readonly array $executors)
    {
    }

    public function supports(RefactoringCommand $command): bool
    {
        foreach ($this->executors as $executor) {
            if ($executor->supports($command)) {
                return true;
            }
        }

        return false;
    }

    public function handle(RefactoringCommand $command): void
    {
        foreach ($this->executors as $executor) {
            if ($executor->supports($command)) {
                $executor->handle($command);
                return;
            }
        }

        throw new \RuntimeException(sprintf('No executor found for command %s', get_class($command)));
    }
}
