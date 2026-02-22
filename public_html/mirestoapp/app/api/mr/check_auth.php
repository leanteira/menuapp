<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

$is_logged_in = isset($_SESSION['cliente_id']);

mr_json_response([
    'is_logged_in' => $is_logged_in,
    'cliente_id' => $is_logged_in ? (int) $_SESSION['cliente_id'] : null,
    'nombre' => $is_logged_in ? (string) $_SESSION['cliente_nombre'] : null,
    'telefono' => $is_logged_in ? (string) ($_SESSION['cliente_telefono'] ?? '') : null,
    'email' => $is_logged_in ? (string) ($_SESSION['cliente_email'] ?? '') : null,
]);
