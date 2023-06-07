<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests');

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'phpdoc_to_comment' => false,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'array_syntax' => ['syntax' => 'short'],
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
    ->setFinder($finder);