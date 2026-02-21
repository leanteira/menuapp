<?php

// Configuración de la base de datos
$host = 'localhost';
$db = 'serv_mirestoapp'; // Cambia por el nombre de tu base de datos
$user = 'serv_mirestoapp'; // Cambia por tu usuario de base de datos
$password = 'y5b9n*G5sC'; // Cambia por tu contraseña de base de datos

// Conexión a la base de datos
$conn = mysqli_connect($host, $user, $password, $db);

// Verificar conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// (Charset por defecto de la base; se retira forzado utf8mb4 a pedido del usuario)
