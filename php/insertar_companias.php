<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

// Insertar compañía: espera campos nombre, pais, descripcion, id_videojuego
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    $pais = isset($_POST['pais']) ? $_POST['pais'] : null;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $id_videojuego = isset($_POST['id_videojuego']) && $_POST['id_videojuego'] !== '' ? $_POST['id_videojuego'] : null;

    $sql = 'INSERT INTO "compania" ("nombre", "pais", "descripcion", "id_videojuego") VALUES ($1, $2, $3, $4)';
    $res = pg_query_params($conexion, $sql, array($nombre, $pais, $descripcion, $id_videojuego));

    if ($res) {
        echo "ok";
    } else {
        // return error message for debugging
        echo "error: " . pg_last_error($conexion);
    }
} else {
    echo "error: missing fields";
}

pg_close($conexion);
?>
