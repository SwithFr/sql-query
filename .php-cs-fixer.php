<?php

$finder = PhpCsFixer\Finder::create()->exclude('vendor')->in(__DIR__);

return (new PhpCsFixer\Config())->setRules([
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'new_line_for_chained_calls',
    ],
    'trailing_comma_in_multiline' => [
        'elements' => ['arrays'],
    ],
    'not_operator_with_successor_space' => true,
    'class_attributes_separation' => [
        'elements' => [
            'property' => 'one',
            'method' => 'one',
            'const' => 'only_if_meta',
            'trait_import' => 'none',
        ],
    ],
])->setFinder($finder);