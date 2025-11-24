<?php
header('Content-Type: application/json');
require_once "conexion.php";

// Alias "contraseña" as contrasena to simplify JSON keys (no accented keys)
$sql = 'SELECT 
            "id_cliente", 
            "nombre", 
            "email", 
            "contraseña", 
            "telefono", 
            "direccion", 
            "fecha_registro", 
            "id_pedido"
        FROM clientes';

$result = pg_query($conexion, $sql);

$clientes = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $clientes[] = $row;
    }
} else {
    echo json_encode(["data" => [], "error" => pg_last_error($conexion)]);
    pg_close($conexion);
    exit;
}

echo json_encode(["data" => $clientes]);
pg_close($conexion);
?>
