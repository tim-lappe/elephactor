<?php

namespace TimLappe\ElephactorTests\Application;

use PHPUnit\Framework\TestCase;
use TimLappe\Elephactor\Application;

class CommandTestCase extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $projectManager = ProjectManager::createMinimalProject();
        $this->application = new Application($projectManager->getTemporaryTargetDirectory());
    }

    protected function getApplication(): Application
    {
        return $this->application;
    }
}