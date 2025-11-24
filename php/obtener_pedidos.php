<?php
header('Content-Type: application/json');
require_once "conexion.php";

$sql = 'SELECT 
            "id_pedido", 
            "fecha_pedido", 
            "total", 
            "estado", 
            "id_videojuego"
        FROM pedidos';

$result = pg_query($conexion, $sql);

$pedidos = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $pedidos[] = $row;
    }
} else {
    echo json_encode(["data" => [], "error" => pg_last_error($conexion)]);
    pg_close($conexion);
    exit;
}

echo json_encode(["data" => $pedidos]);
pg_close($conexion);
?>
