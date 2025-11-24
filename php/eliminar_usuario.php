<?php
// Evitar impresión de HTML y forzar salida limpia
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once "conexion.php";

if (!isset($conexion) || !$conexion) {
    error_log('eliminar_usuario.php: conexión inválida');
    echo 'error';
    exit;
}

$id = isset($_POST['idUsuario']) ? $_POST['idUsuario'] : (isset($_POST['id']) ? $_POST['id'] : '');

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    echo 'error';
    exit;
}

$sql = 'DELETE FROM public."usuarios" WHERE "idUsuario" = $1';
$result = pg_query_params($conexion, $sql, [$id]);

if ($result === false) {
    $err = pg_last_error($conexion);
    error_log('eliminar_usuario.php SQL error: ' . $err);
    echo 'error';
    pg_close($conexion);
    exit;
}

$affected = pg_affected_rows($result);
pg_close($conexion);

if ($affected > 0) {
    echo 'ok';
} else {
    echo 'no_encontrado';
}
?>