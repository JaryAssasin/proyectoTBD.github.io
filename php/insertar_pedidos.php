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
// Iniciar transacción
pg_query($conexion, 'BEGIN');

try {
    // Vamos a crear UN pedido por cada item (el cliente ve un solo pedido, pero en la BD queda uno por juego)
    $sqlInsertPedido = 'INSERT INTO pedidos ("fecha_pedido","total","estado","id_videojuego") VALUES (CURRENT_DATE, $1, $2, $3) RETURNING id_pedido';
    $sqlStock = 'UPDATE videojuegos SET existencia = existencia - $1 WHERE id_videojuego = $2 AND existencia >= $1';

    $inserted = [];

    foreach ($items as $it) {
        $id = $it['id'];
        $cantidad = intval($it['cantidad'] ?? 1);
        $precio = floatval($it['precio'] ?? 0);
        $itemTotal = $precio * $cantidad;

        // Insertar un pedido por cada artículo
        $res = pg_query_params($conexion, $sqlInsertPedido, array($itemTotal, 'Pendiente', $id));
        if (!$res) throw new Exception(pg_last_error($conexion));
        $row = pg_fetch_assoc($res);
        $id_pedido = $row['id_pedido'];
        $inserted[] = $id_pedido;

        // Actualizar stock (verifica existencia suficiente)
        $r2 = pg_query_params($conexion, $sqlStock, array($cantidad, $id));
        if (!$r2) throw new Exception(pg_last_error($conexion));
        if (pg_affected_rows($r2) == 0) {
            throw new Exception("Stock insuficiente para videojuego id=$id");
        }
    }

    pg_query($conexion, 'COMMIT');

    // Devolver el primer id_pedido para compatibilidad con frontend (ticket.php?id=...)
    $first = count($inserted) ? $inserted[0] : null;
    echo json_encode(["success" => true, "id_pedido" => $first, "pedidos" => $inserted]);

} catch (Exception $e) {
    pg_query($conexion, 'ROLLBACK');
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

pg_close($conexion);
?>
