<?php

return [
    'config' => [
        \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class => [
            'default' => 'align',
        ],
    ],
    'remove' => [
        \SlevomatCodingStandard\Sniffs\Classes\SuperfluousAbstractClassNamingSniff::class,
    ],
];
