<?php
// include-user.php

// Iniciar la sesión si no se ha iniciado
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
include('include-db_connection.php');

// Inicializar variables
$rol_usuario = '';
$id_club_usuario = 0;
$nombre_club = '';

// Verificar si el usuario está autenticado
if (isset($_SESSION['id'])) {
    // Recuperar el rol y el club del usuario
    $sql_usuario = "SELECT rol FROM usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    if ($stmt_usuario) {
        $stmt_usuario->bind_param("i", $_SESSION['id']);
        $stmt_usuario->execute();
        $result_usuario = $stmt_usuario->get_result();
        if ($row_usuario = $result_usuario->fetch_assoc()) {
            $rol_usuario = $row_usuario['rol'];
        }
        $stmt_usuario->close();
    } else {
        // Manejar el error en la preparación de la consulta
        echo "<script>alert('Error al preparar la consulta del usuario: " . $conn->error . "');</script>";
    }

} else {
    // Manejar usuarios no autenticados si es necesario
    // Por ejemplo, redirigir a la página de inicio de sesión
    header("Location: login.php");
    exit();
}
?>
