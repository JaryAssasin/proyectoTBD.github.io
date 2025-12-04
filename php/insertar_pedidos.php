<?php
header('Content-Type: application/json');
require_once 'conexion.php';

// Leer input: soporta JSON o form-urlencoded
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    // intentar leer como form-data tradicional
    $data = $_POST;
}

// Esperamos: total y items (items puede venir como array si JSON, o no si POST individual)
if (!isset($data['total'])) {
    echo json_encode(["success" => false, "error" => "Falta total"]);
    exit;
}

// Normalizar items: si viene como string JSON en POST, decodificar
if (!isset($data['items']) && isset($_POST['items'])) {
    $maybe = $_POST['items'];
    $decoded = json_decode($maybe, true);
    $data['items'] = $decoded ?: [];
}

$total = $data['total'];
$items = $data['items'] ?? [];

// Si no hay items, permitir insertar 1 pedido simple si se envía id_videojuego
if (empty($items) && isset($data['id_videojuego'])) {
    $items = [
        ["id" => $data['id_videojuego'], "cantidad" => $data['cantidad'] ?? 1, "precio" => $data['precio'] ?? 0]
    ];
}

if (empty($items)) {
    echo json_encode(["success" => false, "error" => "No hay items"]);
    exit;
}

// Iniciar transacción
pg_query($conexion, 'BEGIN');

try {
    // Insertar pedido principal
    $sqlPedido = 'INSERT INTO pedidos ("fecha_pedido","total","estado") VALUES (CURRENT_DATE, $1, $2) RETURNING id_pedido';
    $res = pg_query_params($conexion, $sqlPedido, array($total, 'Pendiente'));
    if (!$res) throw new Exception(pg_last_error($conexion));

    $row = pg_fetch_assoc($res);
    $id_pedido = $row['id_pedido'];

    // Insertar detalles (si no existe la tabla detalle_pedidos, el INSERT fallará)
    $sqlDetalle = 'INSERT INTO detalle_pedidos ("id_pedido","id_videojuego","cantidad","precio_unitario") VALUES ($1,$2,$3,$4)';
    $sqlStock   = 'UPDATE videojuegos SET existencia = existencia - $1 WHERE id_videojuego = $2 AND existencia >= $1';

    foreach ($items as $it) {
        $id = $it['id'];
        $cantidad = intval($it['cantidad'] ?? 1);
        $precio = floatval($it['precio'] ?? 0);

        // Insert detalle
        $r = pg_query_params($conexion, $sqlDetalle, array($id_pedido, $id, $cantidad, $precio));
        if (!$r) throw new Exception(pg_last_error($conexion));

        // Actualizar stock (verifica existencia suficiente)
        $r2 = pg_query_params($conexion, $sqlStock, array($cantidad, $id));
        if (!$r2) throw new Exception(pg_last_error($conexion));
        if (pg_affected_rows($r2) == 0) {
            throw new Exception("Stock insuficiente para videojuego id=$id");
        }
    }

    pg_query($conexion, 'COMMIT');
    echo json_encode(["success" => true, "id_pedido" => $id_pedido]);

} catch (Exception $e) {
    pg_query($conexion, 'ROLLBACK');
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

pg_close($conexion);
?>
