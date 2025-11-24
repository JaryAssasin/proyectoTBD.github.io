<?php
header('Content-Type: application/json');
require_once "conexion.php";

$response = ["status" => "error", "msg" => "Datos incompletos"];

// Espera: nombre, email, contrasena, telefono, direccion, id_pedido (id_pedido opcional)
if (isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['contrasena'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    // use non-accented POST key coming from the form
    $contrasena = $_POST['contrasena'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
    $id_pedido = isset($_POST['id_pedido']) && $_POST['id_pedido'] !== '' ? $_POST['id_pedido'] : null;

    $sql = 'INSERT INTO clientes("nombre", "email", "contraseña", "telefono", "direccion", "fecha_registro", "id_pedido") VALUES ($1, $2, $3, $4, $5, CURRENT_DATE, $6)';
    $result = pg_query_params($conexion, $sql, array($nombre, $email, $contrasena, $telefono, $direccion, $id_pedido));

    if ($result) {
        $response = ["status" => "ok", "msg" => "Cliente creado correctamente"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
