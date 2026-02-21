<?php
for ($i = 61; $i <= 5000; $i++) {
    $id_jugador = rand(1, 5000); // Simular jugadores existentes
    $id_club = rand(1, 10); // Simular rango de club
    $id_disciplina = rand(1, 5); // Disciplina aleatoria
    $id_zona = rand(1, 20); // Zona aleatoria
    $id_division = rand(1, 10); // División aleatoria

    // Generar fechas aleatorias
    $fecha_fichaje = date('Y-m-d', strtotime("-" . rand(0, 365) . " days"));
    $fecha_vencimiento = date('Y-m-d', strtotime($fecha_fichaje . " + 1 year"));

    // Generar estados aleatorios
    $estados = ['Validado', 'Pendiente', 'Rechazado'];
    $estado = $estados[array_rand($estados)];

    echo "INSERT INTO `fichajes`(`id_fichaje`, `id_jugador`, `id_club`, `id_disciplina`, `id_zona`, `id_division`, `fecha_fichaje`, `fecha_vencimiento`, `estado`) VALUES ($i, $id_jugador, $id_club, $id_disciplina, $id_zona, $id_division, '$fecha_fichaje', '$fecha_vencimiento', '$estado');\n\n";
}
?>