<?php

return [
    'default_input_format' => 'tsv',
    'default_output_format' => 'tsv',
    'supported_formats' => ['tsv', 'json', 'xml'],
    'memory_limit' => '256M',
    'logging' => [
        'enabled' => true,
        'path' => __DIR__ . '/../var/logs/app.log',
        'level' => 'info'
    ],
    'mapping' => [
        'default_file' => __DIR__ . '/../data/mappings/ipa.tsv',
        'encoding' => 'UTF-8'
    ],
    'output' => [
        'directory' => __DIR__ . '/../data/output',
        'padding' => 25
    ]
]; 