<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor', 'runtime', 'tests/_output', 'tests/_data', 'web/assets'])
    ->name('*.php');

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'single_blank_line_at_eof' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'foreach', 'while', 'do', 'switch'],
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'after_heredoc' => true,
        ],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'method' => 'one',
                'property' => 'one',
            ]
        ],
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true); 