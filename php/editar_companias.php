<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

// Editar compañía: espera id_compania, nombre, pais, descripcion, id_videojuego
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_compania'])) {
    $id = $_POST['id_compania'];
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $pais = isset($_POST['pais']) ? $_POST['pais'] : null;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $id_videojuego = isset($_POST['id_videojuego']) && $_POST['id_videojuego'] !== '' ? $_POST['id_videojuego'] : null;

    // ✅ Corregido: el campo correcto es "id_compañia" (minúsculas y con ñ)
    $sql = 'UPDATE "compania"
            SET "nombre" = COALESCE($1, "nombre"),
                "pais" = COALESCE($2, "pais"),
                "descripcion" = COALESCE($3, "descripcion"),
                "id_videojuego" = $4
            WHERE "id_compañia" = $5';

    $res = pg_query_params($conexion, $sql, array($nombre, $pais, $descripcion, $id_videojuego, $id));

    if ($res) {
        echo "ok";
    } else {
        echo "error: " . pg_last_error($conexion);
    }
} else {
    echo "error: missing fields";
}

pg_close($conexion);
?>
