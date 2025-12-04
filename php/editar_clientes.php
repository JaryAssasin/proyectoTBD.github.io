<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ["status" => "error", "msg" => "Datos incompletos"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {

    $id = $_POST['id_cliente'];
    $nombre = $_POST['nombre'] ?? null;
    $email = $_POST['email'] ?? null;
    $contrasena = $_POST['contrasena'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $id_pedido = ($_POST['id_pedido'] !== '') ? $_POST['id_pedido'] : null;

    $sql = 'UPDATE clientes 
            SET "nombre" = $1, 
                "email" = $2, 
                "contraseña" = $3, 
                "telefono" = $4, 
                "direccion" = $5, 
                "id_pedido" = $6
            WHERE "id_cliente" = $7';

    $result = pg_query_params(
        $conexion,
        $sql,
        array($nombre, $email, $contrasena, $telefono, $direccion, $id_pedido, $id)
    );

    if ($result) {
        $response = ["status" => "ok", "msg" => "Cliente actualizado correctamente"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
?>