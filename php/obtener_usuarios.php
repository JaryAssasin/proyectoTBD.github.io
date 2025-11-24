<?php
header('Content-Type: application/json');

// Incluir conexión
require_once "conexion.php";

// Consulta — sin mostrar la contraseña
// Usar el nombre correcto de la tabla: public."usuarios"
// Agregar un campo "departamento" vacío para mantener compatibilidad con DataTables
$sql = "SELECT \"idUsuario\", \"nombreUsuario\", \"Nombre\", \"rol\", '' AS \"departamento\" FROM public.\"usuarios\" ORDER BY \"idUsuario\" ASC";

$result = pg_query($conexion, $sql);

$usuarios = [];

if ($result === false) {
    // En caso de error en la consulta, devolver un mensaje útil para debug
    $error = pg_last_error($conexion);
    echo json_encode(["data" => [], "error" => $error]);
    pg_close($conexion);
    exit;
}

// Usar pg_fetch_all cuando sea posible para obtener todos los registros de forma fiable
$rows = pg_fetch_all($result);
if ($rows !== false) {
    $usuarios = $rows;
} else {
    // Si no hay filas, pg_fetch_all devuelve false — mantener array vacío
    $usuarios = [];
}

// Enviar datos en formato JSON para DataTables
echo json_encode(["data" => $usuarios, "count" => count($usuarios)]);

// Cerrar conexión
pg_close($conexion);
?>
