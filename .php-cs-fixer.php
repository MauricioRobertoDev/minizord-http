<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                                      => true,
        'array_indentation'                           => true,
        'array_syntax'                                => ['syntax' => 'short'],
        'combine_consecutive_unsets'                  => true,
        'class_attributes_separation'                 => ['elements' => ['method' => 'one']],
        'multiline_whitespace_before_semicolons'      => false,
        'single_quote'                                => true,
        'binary_operator_spaces'                      => [
            'operators' => [
                '=>'   => 'align',
                '='    => 'align',
                '??='  => 'align',
            ],
        ],
        'blank_line_after_opening_tag'                => true,
        'blank_line_before_statement'                 => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'braces'                                      => [
            'allow_single_line_closure' => true,
        ],
        'cast_spaces'                                 => true,
        'class_definition'                            => ['single_line' => true, 'inline_constructor_arguments' => true],
        'concat_space'                                => ['spacing' => 'one'],
        'declare_equal_normalize'                     => true,
        'function_typehint_space'                     => true,
        'single_line_comment_style'                   => ['comment_types' => ['hash']],
        'include'                                     => true,
        'lowercase_cast'                              => true,
        'native_function_casing'                      => true,
        'new_with_braces'                             => true,
        'no_blank_lines_after_class_opening'          => true,
        'no_blank_lines_after_phpdoc'                 => true,
        'no_blank_lines_before_namespace'             => false,
        'no_empty_comment'                            => true,
        'no_empty_phpdoc'                             => true,
        'no_trailing_whitespace_in_comment'           => true,
        'no_empty_statement'                          => true,
        'single_line_after_imports'                   => true,
        'ordered_imports'                             => true,
        'single_space_after_construct'                => true,
        'no_extra_blank_lines'                        => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                // 'parenthesis_brace_block',
                // 'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_leading_import_slash'                     => true,
        'no_leading_namespace_whitespace'             => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_singleline_whitespace_before_semicolons'  => true,
        'no_spaces_around_offset'                     => true,
        'no_trailing_comma_in_list_call'              => true,
        'no_trailing_comma_in_singleline_array'       => true,
        'no_unneeded_control_parentheses'             => true,
        'no_unused_imports'                           => true,
        'no_whitespace_before_comma_in_array'         => true,
        'no_whitespace_in_blank_line'                 => true,
        'normalize_index_brace'                       => true,
        'object_operator_without_whitespace'          => true,
        'phpdoc_align'                                => true,
        'phpdoc_annotation_without_dot'               => true,
        'phpdoc_indent'                               => true,
        'phpdoc_inline_tag_normalizer'                => true,
        'no_trailing_whitespace_in_comment'           => true,
        'phpdoc_tag_casing'                           => true,
        'phpdoc_var_annotation_correct_order'         => true,
        'phpdoc_return_self_reference'                => true,
        'phpdoc_scalar'                               => true,
        'phpdoc_single_line_var_spacing'              => true,
        'phpdoc_summary'                              => true,
        'phpdoc_to_comment'                           => true,
        'phpdoc_separation'                           => true,
        'phpdoc_trim'                                 => true,
        'phpdoc_types'                                => true,
        'phpdoc_var_without_name'                     => true,
        'no_singleline_whitespace_before_semicolons'  => true,
        'no_trailing_whitespace'                      => true,
        'return_type_declaration'                     => ['space_before' => 'none'],
        'single_blank_line_before_namespace'          => true,
        'single_class_element_per_statement'          => true,
        'space_after_semicolon'                       => true,
        'ternary_operator_spaces'                     => true,
        'trailing_comma_in_multiline'                 => true,
        'trim_array_spaces'                           => true,
        'unary_operator_spaces'                       => true,
        'whitespace_after_comma_in_array'             => true,
        'space_after_semicolon'                       => true,
        'not_operator_with_successor_space'           => true,

    ])
    ->setFinder($finder)
    ->setLineEnding("\n");
