<?php
// Incluir conexión a la base de datos
include('include-db_connection.php');

// Verificar si el equipo "Libre" ya existe
$libre_nombre = "Libre";
$sql_check_libre = "SELECT id_club FROM clubes WHERE nombre = ?";
$stmt_check_libre = $conn->prepare($sql_check_libre);
$stmt_check_libre->bind_param("s", $libre_nombre);
$stmt_check_libre->execute();
$result_check_libre = $stmt_check_libre->get_result();

if ($result_check_libre->num_rows === 0) {
    // Insertar el equipo "Libre"
    $sql_insert_libre = "INSERT INTO clubes (nombre) VALUES (?)";
    $stmt_insert_libre = $conn->prepare($sql_insert_libre);
    $stmt_insert_libre->bind_param("s", $libre_nombre);
    $stmt_insert_libre->execute();
    
    if ($stmt_insert_libre->affected_rows === 1) {
        echo "Equipo 'Libre' creado con ID: " . $stmt_insert_libre->insert_id;
    } else {
        echo "Error al crear el equipo 'Libre'.";
    }
    
    $stmt_insert_libre->close();
} else {
    echo "El equipo 'Libre' ya existe con ID: " . $result_check_libre->fetch_assoc()['id_club'];
}

$stmt_check_libre->close();
?>
