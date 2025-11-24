<?php
include 'conexion.php';
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = $_POST['nombreUsuario'];
    $nombre = $_POST['Nombre'];
    $contrasena = $_POST['contraseña'];
    $rol = $_POST['rol'];
    // Some forms send a 'departamento' field for UI reasons but the DB schema
    // for public."usuarios" does not include it. We'll accept it but not store it.

    // Insertar y devolver el id generado para diagnóstico
    $query = 'INSERT INTO public."usuarios" ("nombreUsuario", "Nombre", "contraseña", "rol") VALUES ($1, $2, $3, $4) RETURNING "idUsuario"';
    $result = pg_query_params($conexion, $query, array($nombreUsuario, $nombre, $contrasena, $rol));

    if ($result) {
        $inserted = pg_fetch_assoc($result);
        $newId = $inserted['idUsuario'] ?? null;
        $response['status'] = 'ok';
        $response['msg'] = 'Usuario creado exitosamente';
        $response['idUsuario'] = $newId;
    } else {
        $response['status'] = 'error';
        $response['msg'] = 'Error al crear el usuario';
        $response['error'] = pg_last_error($conexion);
    }
} else {
    $response['status'] = 'error';
    $response['msg'] = 'Método no permitido';
}

echo json_encode($response);
?>