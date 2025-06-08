<?php

namespace TimLappe\ElephactorTests\Application;

use TimLappe\Elephactor\Model\RealDirectory;

class ProjectManager
{
    private function __construct(
        private RealDirectory $originalProjectRoot,
        private string $temporaryTargetDirectory,
    ) {

        if (!file_exists($this->originalProjectRoot->getPath() . '/composer.json')) {
            throw new \RuntimeException('Project root does not contain a composer.json file');
        }

        if (!file_exists($this->originalProjectRoot->getPath() . '/vendor/autoload.php')) {
            throw new \RuntimeException('Project root does not contain a vendor/autoload.php file');
        }

        require_once $this->originalProjectRoot->getPath() . '/vendor/autoload.php';

        $this->copyProject();
    }

    private function copyProject(): void
    {
        if (!is_dir($this->temporaryTargetDirectory)) {
            mkdir($this->temporaryTargetDirectory, 0777, true);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->originalProjectRoot->getPath(),
                \RecursiveDirectoryIterator::SKIP_DOTS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if (!$item instanceof \SplFileInfo) {
                continue;
            }

            $targetPath = $this->temporaryTargetDirectory . '/' . $iterator->getSubPathName();

            if ($item instanceof \SplFileInfo) {
                $targetPath = $this->temporaryTargetDirectory . '/' . $iterator->getSubPathName();
            }
            
            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0777, true);
                }
            } else {
                copy($item->getPathname(), $targetPath);
            }
        }
    }

    public function getOriginalProjectRoot(): RealDirectory
    {
        return $this->originalProjectRoot;
    }

    public function getTemporaryTargetDirectory(): RealDirectory
    {
        return new RealDirectory($this->temporaryTargetDirectory);
    }

    public static function createMinimalProject(): self
    {
        $tmpDir = sys_get_temp_dir() . '/elephactor-test-' . uniqid();
        mkdir($tmpDir, 0777, true);

        return new self(new RealDirectory(__DIR__ . '/TestProjects/Minimal'), $tmpDir);
    }
}