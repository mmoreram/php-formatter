<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_multiline_whitespace_before_semicolons' => false,
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => false,
        'return_type_declaration' => ['space_before' => 'one'],
        'self_accessor' => false,
        'no_extra_consecutive_blank_lines' => false,
    ])
    ->setFinder($finder)
;
