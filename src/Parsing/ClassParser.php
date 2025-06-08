<?php

namespace TimLappe\Elephactor\Parsing;

use PhpParser\Node;
use PhpParser\ParserFactory;
use TimLappe\Elephactor\Model\Environment;
use TimLappe\Elephactor\Model\LoadedClass;

class ClassParser
{
    public function __construct(
        private readonly Environment $environment,
    ) {
    }

    /**
     * @return Node[]
     */
    public function parse(LoadedClass $loadedClass): array
    {
        $parser = (new ParserFactory())->createForVersion($this->environment->getTargetPhpVersion()->toNikicPhpParserVersion());
        return $parser->parse($loadedClass->getFile()->readContent()) ?? throw new \RuntimeException(sprintf('Could not parse file %s', $loadedClass->getFile()->getPath()));
    }
}