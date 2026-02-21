<?php

return [
    'db' => [
        'host' => getenv('MR_DB_HOST') ?: 'localhost',
        'name' => getenv('MR_DB_NAME') ?: 'serv_mirestoapp',
        'user' => getenv('MR_DB_USER') ?: 'serv_mirestoapp',
        'pass' => getenv('MR_DB_PASS') ?: 'y5b9n*G5sC',
        'port' => (int) (getenv('MR_DB_PORT') ?: 3306),
    ],
    'app' => [
        'name' => 'MiRestoApp',
        'timezone' => 'America/Argentina/Buenos_Aires',
    ],
    'mercadopago' => [
        'access_token' => getenv('MR_MP_ACCESS_TOKEN') ?: '',
        'public_key' => getenv('MR_MP_PUBLIC_KEY') ?: '',
        'base_url' => getenv('MR_MP_BASE_URL') ?: 'https://api.mercadopago.com',
        'webhook_url' => getenv('MR_MP_WEBHOOK_URL') ?: '',
        'success_url' => getenv('MR_MP_SUCCESS_URL') ?: '',
        'pending_url' => getenv('MR_MP_PENDING_URL') ?: '',
        'failure_url' => getenv('MR_MP_FAILURE_URL') ?: '',
    ],
];
