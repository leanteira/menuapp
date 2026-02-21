
<?php
for ($i = 101; $i <= 5000; $i++) {
    $nombre = "Nombre_$i";
    $apellido = "Apellido_$i";
    $dni = 20000000 + $i; // Generar DNIs secuenciales
    $sexo = $i % 2 == 0 ? 'Masculino' : 'Femenino'; // Alternar entre masculino y femenino
    $fecha_nacimiento = date('Y-m-d', strtotime('-' . rand(10, 50) . ' years')); // Generar fechas de nacimiento aleatorias

    echo "INSERT INTO `jugadores` (`id_jugador`, `nombre`, `apellido`, `dni`, `sexo`, `fecha_nacimiento`) VALUES ($i, '$nombre', '$apellido', '$dni', '$sexo', '$fecha_nacimiento');\n\n";
 
}
?>
;