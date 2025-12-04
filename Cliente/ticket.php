<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Ticket</title>
<body style="background:#0f1724;color:white;font-family:Inter;padding:20px;">

<h1>📄 Ticket de pedido</h1>

<p>ID de pedido: <span id="idp"></span></p>
<p>Tu pedido fue registrado correctamente.  
Te contactaremos cuando esté listo para entrega.</p>

<a href="index.php"
   style="color:#6ee7b7;font-size:1.2rem;">Volver al catálogo</a>

<script>
const params = new URLSearchParams(window.location.search);
document.getElementById("idp").innerText = params.get("id");
</script>

</body>
</html>
