<?php

return [
    'allowed_origins' => ['http://localhost:8000'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => false,
];

