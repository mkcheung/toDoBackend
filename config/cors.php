<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*', 'login', 'logout', 'user'],
    'allowed_origins' => ['http://localhost:5173'], // Vite dev origin
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];