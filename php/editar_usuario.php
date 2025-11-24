<?php
include 'conexion.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = $_POST['idUsuario'];
    $nombreUsuario = $_POST['nombreUsuario'];
    $nombre = $_POST['Nombre'];
    $contrasena = $_POST['contraseña'];
    $rol = $_POST['rol'];
    // Some forms include 'departamento' but the DB table public."usuarios" does not
    // have that column. We accept the value from the form but don't persist it.
    $query = 'UPDATE public."usuarios" SET "nombreUsuario" = $1, "Nombre" = $2, "contraseña" = $3, "rol" = $4 WHERE "idUsuario" = $5';
    $result = pg_query_params($conexion, $query, array($nombreUsuario, $nombre, $contrasena, $rol, $idUsuario));

    if ($result) {
        $response['status'] = 'ok';
        $response['msg'] = 'Usuario actualizado exitosamente';
    } else {
        $response['status'] = 'error';
        $response['msg'] = 'Error al actualizar el usuario';
        $response['error'] = pg_last_error($conexion);
    }
} else {
    $response['status'] = 'error';
    $response['msg'] = 'Método no permitido';
}

echo json_encode($response);
?>