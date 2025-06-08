<?php

namespace TimLappe\Elephactor\Refactoring;

use PhpParser\NodeTraverser;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Identifier;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\ClassName;
use TimLappe\Elephactor\Model\LoadedClass;
use TimLappe\Elephactor\Parsing\ClassParser;

class ClassNameRefactor
{
    public function __construct(
        private readonly Environment $environment,
    ) {
    }

    public function rename(LoadedClass $loadedClass, ClassName $newName): void
    {
        $parser = new ClassParser($this->environment);
        $ast = $parser->parse($loadedClass);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class($loadedClass->getExistingClassName()->getClassName(), $newName) extends NodeVisitorAbstract {
            public function __construct(
                private ClassName $oldName,
                private ClassName $newName,
            ) {
            }

            public function enterNode(Node $node): Node {
                if ($node instanceof Class_ && $node->name?->toString() === $this->oldName->getShortClassName()) {
                    $node->name = new Identifier($this->newName->getShortClassName());
                }

                return $node;
            }
        });

        $ast = $traverser->traverse($ast);
        $newContent = $this->prettyPrint($ast);

        $loadedClass->getFile()->writeContent($newContent);
        $loadedClass->getFile()->rename($newName->getShortClassName() . '.php');
    }

    /**
     * @param Node[] $ast
     */
    private function prettyPrint(array $ast): string
    {
        $printer = new Standard();
        return $printer->prettyPrintFile($ast);
    }
}