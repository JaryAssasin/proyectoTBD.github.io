<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

// Insertar pedido: espera total y estado, fecha_pedido and id_videojuego opcionales
if (isset($_POST['total']) && isset($_POST['estado'])) {
    $fecha_pedido = isset($_POST['fecha_pedido']) && $_POST['fecha_pedido'] !== '' ? $_POST['fecha_pedido'] : null;
    $total = $_POST['total'];
    $estado = $_POST['estado'];
    $id_videojuego = isset($_POST['id_videojuego']) && $_POST['id_videojuego'] !== '' ? $_POST['id_videojuego'] : null;

    $sql = 'INSERT INTO pedidos ("fecha_pedido", "total", "estado", "id_videojuego") VALUES (COALESCE($1, CURRENT_DATE), $2, $3, $4)';
    $res = pg_query_params($conexion, $sql, array($fecha_pedido, $total, $estado, $id_videojuego));

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