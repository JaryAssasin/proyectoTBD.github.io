<?php
header('Content-Type: text/plain');
require_once 'conexion.php';

if (isset($_POST['id_compania'])) {
    $id = $_POST['id_compania'];

    // OJO: el campo real es "id_compañia", con ñ
    $sql = 'DELETE FROM "compania" WHERE "id_compañia" = $1';
    $res = pg_query_params($conexion, $sql, array($id));

    if ($res) {
        echo "ok";
    } else {
        echo "error: " . pg_last_error($conexion);
    }
} else {
    echo "error: missing id";
}

pg_close($conexion);
?>
