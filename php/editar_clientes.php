<?php
header('Content-Type: text/plain');
require_once "conexion.php";

if (isset($_POST['idAlumno'], $_POST['Nombre'], $_POST['ApellidoPaterno'], $_POST['ApellidoMaterno'], $_POST['Genero'], $_POST['Telefono'], $_POST['Ncontrol'])) {
    $id = $_POST['idAlumno'];
    $nombre = $_POST['Nombre'];
    $apaterno = $_POST['ApellidoPaterno'];
    $amaterno = $_POST['ApellidoMaterno'];
    $genero = $_POST['Genero'];
    $telefono = $_POST['Telefono'];
    $ncontrol = $_POST['Ncontrol'];

    $sql = 'UPDATE alumnos 
            SET "Nombre"=$1, "ApellidoPaterno"=$2, "ApellidoMaterno"=$3, "Genero"=$4, "Telefono"=$5, "Ncontrol"=$6
            WHERE "idAlumno"=$7';
    $result = pg_query_params($conexion, $sql, [$nombre, $apaterno, $amaterno, $genero, $telefono, $ncontrol, $id]);

    if ($result) {
        echo "ok";
    } else {
        echo "error";
    }
} else {
    echo "error";
}

pg_close($conexion);
<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$response = ["status" => "error", "msg" => "Datos incompletos"];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {
    $id = $_POST['id_cliente'];
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
    $direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
    $id_pedido = isset($_POST['id_pedido']) && $_POST['id_pedido'] !== '' ? $_POST['id_pedido'] : null;

    $sql = 'UPDATE clientes SET "nombre" = $1, "email" = $2, "contraseña" = $3, "telefono" = $4, "direccion" = $5, "id_pedido" = $6 WHERE "id_cliente" = $7';
    $result = pg_query_params($conexion, $sql, array($nombre, $email, $contrasena, $telefono, $direccion, $id_pedido, $id));

    if ($result) {
        $response = ["status" => "ok", "msg" => "Cliente actualizado correctamente"];
    } else {
        $response = ["status" => "error", "msg" => pg_last_error($conexion)];
    }
}

echo json_encode($response);
pg_close($conexion);
?>
