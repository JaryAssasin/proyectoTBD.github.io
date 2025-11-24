<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ["status" => "error", "msg" => "Faltan datos"];

if (isset($_POST['id_cliente'])) {
    $id = $_POST['id_cliente'];
    $sql = 'DELETE FROM clientes WHERE "id_cliente" = $1';
    $res = pg_query_params($conexion, $sql, array($id));
    if ($res) {
        $response = ["status" => "ok", "msg" => "Cliente eliminado"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
?>
