<?php
session_start();
header('Content-Type: application/json');

// Verifica si la sesión está activa
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    echo json_encode([
        'loggedIn' => true,
        'usuario' => [
            'nombreUsuario' => $_SESSION['nombreUsuario'],
            'nombre' => $_SESSION['nombre'],
            'rol' => $_SESSION['rol'],
            'departamento' => $_SESSION['departamento']
        ]
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>