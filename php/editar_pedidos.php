<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

// Editar pedido: devuelve plain-text 'ok' o 'error: ...' para coincidir con pedidos.html
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pedido'])) {
    $id = $_POST['id_pedido'];
    $fecha_pedido = isset($_POST['fecha_pedido']) && $_POST['fecha_pedido'] !== '' ? $_POST['fecha_pedido'] : null;
    $total = isset($_POST['total']) ? $_POST['total'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
    $id_videojuego = isset($_POST['id_videojuego']) && $_POST['id_videojuego'] !== '' ? $_POST['id_videojuego'] : null;

    $sql = 'UPDATE pedidos SET "fecha_pedido" = COALESCE($1, "fecha_pedido"), "total" = $2, "estado" = $3, "id_videojuego" = $4 WHERE "id_pedido" = $5';
    $res = pg_query_params($conexion, $sql, array($fecha_pedido, $total, $estado, $id_videojuego, $id));

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