async function finalizarPedido() {
if (cart.length === 0) {
alert("Tu carrito está vacío.");
return;
}

const total = cart.reduce((s, it) => s + it.precio * it.cantidad, 0);

// enviar JSON con items
const res = await fetch("../php/insertar_pedidos.php", {
method: "POST",
headers: { "Content-Type": "application/json" },
body: JSON.stringify({ total: total, items: cart })
});

let data;
try {
data = await res.json();
} catch (e) {
alert("Error de respuesta del servidor.");
console.error(e);
return;
}

if (data.success) {
alert("Pedido realizado correctamente! ID: " + data.id_pedido);
cart = [];
saveCart();
renderCarritoLateral();
// opcional: redirigir al ticket
window.location.href = "ticket.php?id=" + data.id_pedido;
} else {
alert("Error al procesar pedido: " + (data.error || "desconocido"));
console.error(data);
}
}