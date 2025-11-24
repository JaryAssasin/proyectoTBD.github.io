<?php
$conn = pg_connect("host=localhost port=5432 dbname=quintoisc user=postgres password=12345678");

if ($conn) {
    echo "Conexión exitosa!";
} else {
    echo "Error en la conexión";
}
?>
