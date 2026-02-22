<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

// Destruir la sesión
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

mr_json_response([
    'ok' => true,
    'message' => 'Sesión cerrada correctamente.',
]);
