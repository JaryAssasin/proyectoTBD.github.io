<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ["status" => "error", "msg" => "Datos incompletos"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_videojuego'])) {
    $id = $_POST['id_videojuego'];
    $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : null;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $precio = isset($_POST['precio']) ? $_POST['precio'] : null;
    $existencia = isset($_POST['existencia']) ? $_POST['existencia'] : null;
    $plataforma = isset($_POST['plataforma']) ? $_POST['plataforma'] : null;
    $fecha_ingreso = isset($_POST['fecha_ingreso']) && $_POST['fecha_ingreso'] !== '' ? $_POST['fecha_ingreso'] : null;

    $sql = 'UPDATE videojuegos SET "titulo" = COALESCE($1, "titulo"), "descripcion" = COALESCE($2, "descripcion"), "precio" = $3, "existencia" = $4, "plataforma" = $5, "fecha_ingreso" = COALESCE($6, "fecha_ingreso") WHERE "id_videojuego" = $7';
    $res = pg_query_params($conexion, $sql, array($titulo, $descripcion, $precio, $existencia, $plataforma, $fecha_ingreso, $id));

    if ($res) {
        $response = ["status" => "ok", "msg" => "Videojuego actualizado"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
?>