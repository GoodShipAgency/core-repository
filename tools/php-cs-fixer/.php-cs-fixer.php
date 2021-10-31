<?php

$finder = PhpCsFixer\Finder::create()
    ->in(realpath(dirname(dirname(__DIR__))) . '/src');

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);