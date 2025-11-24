<?php
header('Content-Type: application/json');
require_once "conexion.php";

// Normalize column names: alias to snake_case without accents to simplify JS usage
$sql = 'SELECT 
            "id_compañia",
            "nombre",
            "pais",
            "descripcion",
            "id_videojuego"
        FROM "compania"';

    $result = pg_query($conexion, $sql);

    $companias = [];
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $companias[] = $row;
        }
        echo json_encode(["data" => $companias]);
    } else {
        echo json_encode(["data" => [], "error" => pg_last_error($conexion)]);
    }

    pg_close($conexion);
?>
