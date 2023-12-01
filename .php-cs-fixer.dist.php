<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder(
        (new PhpCsFixer\Finder())
            ->ignoreDotFiles(false)
            ->ignoreVCSIgnored(true)
            ->exclude(['fixtures'])
            ->notPath(['phpstan-baseline.php'])
            ->in(__DIR__)
    )
;
