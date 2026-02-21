<?php

require_once __DIR__ . '/mr_bootstrap.php';

function mr_is_logged_in()
{
    return !empty($_SESSION['mr_user_id']);
}

function mr_user()
{
    if (!mr_is_logged_in()) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['mr_user_id'],
        'restaurante_id' => isset($_SESSION['mr_restaurante_id']) ? (int) $_SESSION['mr_restaurante_id'] : null,
        'nombre' => $_SESSION['mr_nombre'] ?? '',
        'email' => $_SESSION['mr_email'] ?? '',
        'rol' => $_SESSION['mr_rol'] ?? '',
    ];
}

function mr_require_auth($roles = [])
{
    if (!mr_is_logged_in()) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            mr_json_response([
                'ok' => false,
                'error' => 'No autenticado.',
            ], 401);
        }

        header('Location: login.php');
        exit;
    }

    if (!empty($roles)) {
        $currentRole = $_SESSION['mr_rol'] ?? '';
        if (!in_array($currentRole, $roles, true)) {
            if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
                mr_json_response([
                    'ok' => false,
                    'error' => 'No autorizado para esta acción.',
                ], 403);
            }

            http_response_code(403);
            echo 'No autorizado.';
            exit;
        }
    }
}

function mr_logout()
{
    unset(
        $_SESSION['mr_user_id'],
        $_SESSION['mr_restaurante_id'],
        $_SESSION['mr_nombre'],
        $_SESSION['mr_email'],
        $_SESSION['mr_rol'],
        $_SESSION['id'],
        $_SESSION['nombre'],
        $_SESSION['apellido'],
        $_SESSION['email'],
        $_SESSION['rol']
    );
}
