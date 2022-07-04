<?php
$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR2' => true,
        'braces' => [
            'allow_single_line_closure'=>true,
            'position_after_functions_and_oop_constructs' => 'same'
        ],
    ])
;
