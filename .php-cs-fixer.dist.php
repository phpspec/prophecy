<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PER-CS' => true,
        'array_syntax' => false,
        'braces_position' => [
            'control_structures_opening_brace' => 'same_line',
        ],
        'concat_space' => ['spacing' => 'none'],
        'method_argument_space' => ['on_multiline' => 'ignore'],
        // Since PHP 7.2 is supported we can't add trailing commas in arguments, parameters and match
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'modifier_keywords' => false,
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
