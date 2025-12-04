<?php
// Obtener ID del videojuego
$id = $_GET['id'] ?? 0;
if (!$id) die("ID inválido");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprar videojuego</title>
<style>
body{font-family:Arial;background:#0f1724;color:#fff;padding:20px}
.container{max-width:600px;margin:auto;background:#0b1220;padding:20px;border-radius:12px}
input,select,button{padding:8px;border-radius:8px;width:100%;margin-top:12px}
button{background:#6ee7b7;color:#000;font-weight:bold;border:none;cursor:pointer}
button:hover{opacity:.8}
</style>
</head>
<body>
<div class="container">
  <h2>Realizar pedido</h2>

  <div id="game"></div>

  <form id="form">
    <label>Cantidad:</label>
    <input type="number" id="cantidad" min="1" value="1">

    <button type="submit">Confirmar pedido</button>
  </form>
</div>

<script>
const id = <?= $id ?>;

async function cargarJuego() {
  const res = await fetch(`../php/obtener_videojuegos.php?id=${id}`);
  const data = await res.json();
  const g = data.data[0];

  document.getElementById("game").innerHTML = `
    <h3>${g.titulo}</h3>
    <p>Plataforma: ${g.plataforma}</p>
    <p>Precio: $${g.precio}</p>
    <p>Stock disponible: ${g.existencia}</p>
  `;
}

document.getElementById("form").addEventListener("submit", async (e)=>{
  e.preventDefault();
  const cantidad = parseInt(document.getElementById("cantidad").value);

  const res = await fetch("../php/realizar_pedido.php", {
    method:"POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({ id_videojuego:id, cantidad:cantidad })
  });

  const data = await res.json();

  if (data.ok) {
    window.location.href = `ticket.php?id=${data.id_pedido}`;
  } else {
    alert("Error: "+data.error);
  }
});

cargarJuego();
</script>
</body>
</html>
