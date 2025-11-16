<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'phpdoc_align' => true,
        'yoda_style' => false,
        'no_unused_imports' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests'
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
    )
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setUsingCache(true);
