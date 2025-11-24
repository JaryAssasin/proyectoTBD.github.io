<?php
session_start();

// Evitar que PHP muestre errores en HTML en la respuesta JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

$response = array();

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    $response = array(
        'success' => false,
        'message' => 'Falta usuario o contraseña'
    );
} else {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Log básico (no incluir contraseñas en logs)
    error_log("Login attempt - Username: $username");

    // Usar el nombre correcto de la tabla: "usuarios"
    $query = 'SELECT * FROM public."usuarios" WHERE "nombreUsuario" = $1 AND "contraseña" = $2';
    $result = @pg_query_params($conexion, $query, array($username, $password));

    if ($result === false) {
        // Registrar el error detallado en logs del servidor, pero devolver mensaje genérico al cliente
        $pgErr = pg_last_error($conexion);
        error_log("pg_query_params failed in login.php: " . $pgErr);
        $response = array(
            'success' => false,
            'message' => 'Error en el servidor'
        );
    } else {
        if (pg_num_rows($result) > 0) {
            $usuario = pg_fetch_assoc($result);

            // Guardar en sesión (solo campos que sí existen)
            $_SESSION['loggedin'] = true;
            $_SESSION['nombreUsuario'] = $usuario['nombreUsuario'];
            $_SESSION['nombre'] = $usuario['Nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            $response = array(
                'success' => true,
                'message' => 'Login exitoso',
                'usuario' => array(
                    'nombreUsuario' => $usuario['nombreUsuario'],
                    'Nombre' => $usuario['Nombre'],
                    'rol' => $usuario['rol']
                )
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos'
            );
        }
    }
}

echo json_encode($response);
exit;
?>
