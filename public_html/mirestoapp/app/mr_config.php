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
    'email' => [
        'enabled' => (bool) (getenv('MR_EMAIL_ENABLED') ?: false),
        'host' => getenv('MR_EMAIL_HOST') ?: 'vps-1941427-x.dattaweb.com',
        'port' => (int) (getenv('MR_EMAIL_PORT') ?: 587),
        'username' => getenv('MR_EMAIL_USERNAME') ?: 'demo@reeixitone.com.ar',
        'password' => getenv('MR_EMAIL_PASSWORD') ?: 'Ojcoo@a9pY',
        'encryption' => getenv('MR_EMAIL_ENCRYPTION') ?: 'tls',
        'from_email' => getenv('MR_EMAIL_FROM') ?: 'demo@reeixitone.com.ar',
        'from_name' => getenv('MR_EMAIL_FROM_NAME') ?: 'MiRestoApp',
    ],
];
