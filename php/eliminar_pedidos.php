<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

// Eliminar pedido: devuelve 'ok' o 'error: ...' para coincidir con pedidos.html
if (isset($_POST['id_pedido'])) {
    $id = $_POST['id_pedido'];
    $sql = 'DELETE FROM pedidos WHERE "id_pedido" = $1';
    $res = pg_query_params($conexion, $sql, array($id));
    if ($res) {
        echo "ok";
    } else {
        echo "error: " . pg_last_error($conexion);
    }
} else {
    echo "error: missing id_pedido";
}

pg_close($conexion);
?>
?>