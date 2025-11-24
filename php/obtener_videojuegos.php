<?php
header('Content-Type: application/json');
require_once "conexion.php";

// Return snake_case keys so JS can access them consistently
$sql = 'SELECT 
            "id_videojuego", 
            "titulo", 
            "descripcion",
            "precio" ,
            "existencia",
            "plataforma" ,
            "fecha_ingreso"
        FROM "videojuegos"';

$result = pg_query($conexion, $sql);

$videojuegos = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $videojuegos[] = $row;
    }
} else {
    echo json_encode(["data" => [], "error" => pg_last_error($conexion)]);
    pg_close($conexion);
    exit;
}

echo json_encode(["data" => $videojuegos]);
pg_close($conexion);
?>
