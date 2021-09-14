<?php

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR12' => true,
    '@Symfony' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
]);