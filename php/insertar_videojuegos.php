<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ["status" => "error", "msg" => "Datos incompletos"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
    $titulo = $_POST['titulo'];
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
    $precio = isset($_POST['precio']) ? $_POST['precio'] : null;
    $existencia = isset($_POST['existencia']) ? $_POST['existencia'] : null;
    $plataforma = isset($_POST['plataforma']) ? $_POST['plataforma'] : null;
    $fecha_ingreso = isset($_POST['fecha_ingreso']) && $_POST['fecha_ingreso'] !== '' ? $_POST['fecha_ingreso'] : null;

    $sql = 'INSERT INTO videojuegos ("titulo", "descripcion", "precio", "existencia", "plataforma", "fecha_ingreso") VALUES ($1,$2,$3,$4,$5,COALESCE($6, CURRENT_DATE))';
    $res = pg_query_params($conexion, $sql, array($titulo, $descripcion, $precio, $existencia, $plataforma, $fecha_ingreso));

    if ($res) {
        $response = ["status" => "ok", "msg" => "Videojuego creado"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
?>