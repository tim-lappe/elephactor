<?php

namespace TimLappe\ElephactorTests\Application\CommandTests;

use TimLappe\ElephactorTests\Application\CommandTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TimLappe\Elephactor\Command\RenameClass;

class RenameClassTest extends CommandTestCase
{
    public function testRenameClass(): void
    {
        $application = $this->getApplication();
        $command = new RenameClass();
        $command->setApplication($application);

        $input = new ArrayInput([
            'command' => 'rename-class',
            'old-name' => 'TimLappe\\ElephactorSandboxTest\\SimpleTestClass\\TestClass', 
            'new-name' => 'TimLappe\\ElephactorSandboxTest\\SimpleTestClass\\NewTestClass'
        ]);

        $output = new BufferedOutput();
        $command->run($input, $output);

        $this->assertStringContainsString('class NewTestClass', file_get_contents($application->getEnvironment()->getProjectRoot()->getPath() . '/src/SimpleTestClass/NewTestClass.php'));
    }

    public function testRenameClassWithInvalidOldName(): void
    {
        $application = $this->getApplication();
        $command = new RenameClass();
        $command->setApplication($application);

        $input = new ArrayInput([
            'command' => 'rename-class',
            'old-name' => 'TimLappe\\ElephactorSandboxTest\\SimpleTestClass\\InvalidTestClass', 
            'new-name' => 'TimLappe\\ElephactorSandboxTest\\SimpleTestClass\\NewTestClass'
        ]);

        $output = new BufferedOutput();
        $this->expectException(\InvalidArgumentException::class);
        
        $command->run($input, $output);
    }
}