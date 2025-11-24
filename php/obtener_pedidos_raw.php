<?php
header('Content-Type: application/json');
require_once "conexion.php";

$sql = 'SELECT "Id_Pedido" AS id_pedido, "Fecha_Pedido" AS fecha_pedido, "Total" AS total, "Estado" AS estado, "Id_Videojuego" AS id_videojuego FROM "PEDIDOS"';
$result = pg_query($conexion, $sql);

$pedidos = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $pedidos[] = $row;
    }
}

echo json_encode(["data" => $pedidos]);
pg_close($conexion);
?>
