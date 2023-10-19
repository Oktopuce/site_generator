<?php

return [
    'dependencies' => ['core', 'backend'],
    'tags' => [
        'backend.contextmenu',
    ],
    'imports' => [
        '@oktopuce/site-generator/' => 'EXT:site_generator/Resources/Public/JavaScript/',
    ],
];