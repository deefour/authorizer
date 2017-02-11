<?php

use PhpCsFixer\Config;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\Finder\DefaultFinder;

$rules = [
    'blank_line_after_opening_tag'                => true,
    'braces'                                      => true,
    'concat_space'                                => [ 'spacing' => 'one' ],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_empty_statement'                          => true,
    'elseif'                                      => true,
    'phpdoc_no_empty_return'                      => true,
    'encoding'                                    => true,
    'single_blank_line_at_eof'                    => true,
    'no_extra_consecutive_blank_lines'            => true,
    'no_spaces_after_function_name'               => true,
    'function_declaration'                        => true,
    'include'                                     => true,
    'indentation_type'                            => true,
    'blank_line_after_namespace'                  => true,
    'blank_line_before_return'                    => true,
    'no_trailing_comma_in_list_call'              => true,
    'no_trailing_comma_in_singleline_array'       => true,
    'not_operator_with_space'                     => true,
    'lowercase_constants'                         => true,
    'lowercase_keywords'                          => true,
    'method_argument_space'                       => true,
    'trailing_comma_in_multiline_array'           => true,
    'no_multiline_whitespace_before_semicolons'   => true,
    'single_blank_line_before_namespace'          => true,
    'no_blank_lines_after_class_opening'          => true,
    'no_blank_lines_after_phpdoc'                 => true,
    'object_operator_without_whitespace'          => true,
    'no_unused_imports'                           => true,
    'ordered_imports'                             => true,
    'no_unneeded_control_parentheses'             => true,
    'trim_array_spaces'                           => false,
    'phpdoc_align'                                => true,
    'phpdoc_indent'                               => true,
    'phpdoc_inline_tag'                           => true,
    'phpdoc_no_access'                            => true,
    'phpdoc_no_package'                           => true,
    'phpdoc_scalar'                               => true,
    'phpdoc_summary'                              => true,
    'phpdoc_to_comment'                           => true,
    'phpdoc_trim'                                 => true,
    'phpdoc_no_alias_tag'                         => [ 'type' => 'var' ],
    'phpdoc_var_without_name'                     => true,
    'no_leading_import_slash'                     => true,
    'self_accessor'                               => true,
    'array_syntax'                                => [ 'syntax' => 'short' ],
    'no_short_echo_tag'                           => true,
    'single_blank_line_before_namespace'          => true,
    'single_import_per_statement'                 => true,
    'single_line_after_imports'                   => true,
    'single_quote'                                => true,
    'cast_spaces'                                 => false,
    'standardize_not_equals'                      => true,
    'ternary_operator_spaces'                     => true,
    'no_trailing_whitespace'                      => true,
    'unary_operator_spaces'                       => true,
    'visibility_required'                         => true,

    'binary_operator_spaces' => ['align_equals' => true, 'align_double_arrow' => true],
];

$finder = PhpCsFixer\Finder::create()
    ->in([ 'src', 'stub', 'spec' ]);

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder);
