<?php
// Archivo: php/conexion.php

// Configuración de conexión
$host = "localhost";
$port = "5432";
$dbname = "Videojuegos";  // <-- Nombre de tu base de datos en pgAdmin
$user = "postgres";       // <-- Usuario de PostgreSQL
$password = "12345678";   // <-- Contraseña del usuario

// Crear cadena de conexión
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Intentar conectar
$conexion = pg_connect($conn_string);

// Validar conexión
if (!$conexion) {
    die(json_encode([
        "data" => [],
        "error" => "❌ Error de conexión a la base de datos: " . pg_last_error()
    ]));
}
?>
