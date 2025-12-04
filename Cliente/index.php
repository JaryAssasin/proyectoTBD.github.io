<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Catálogo de Videojuegos</title>

  <style>
    :root {
      --bg: #0f1724;
      --card: #0b1220;
      --accent: #6ee7b7;
      --muted: #9aa4b2
    }

    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: Inter, Segoe UI, Arial;
      background: linear-gradient(180deg, #071122 0%, #0f1724 100%);
      color: #e6eef6;
      padding: 24px
    }

    .container {
      max-width: 1100px;
      margin: 0 auto
    }

    .header {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px
    }

    .title {
      font-size: 1.6rem;
      font-weight: 700;
      letter-spacing: 0.4px
    }

    .controls {
      display: flex;
      gap: 8px;
      align-items: center
    }

    .search {
      padding: 8px 12px;
      border-radius: 10px;
      border: none;
      outline: none;
      width: 260px;
      background: #0b1a2a;
      color: inherit
    }

    .select {
      padding: 8px;
      border-radius: 10px;
      background: #061323;
      color: inherit;
      border: none
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 16px
    }

    .card {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(0, 0, 0, 0.05));
      padding: 12px;
      border-radius: 14px;
      box-shadow: 0 6px 18px rgba(2, 6, 23, 0.6);
      transition: transform .15s
    }

    .card:hover {
      transform: translateY(-6px)
    }

    .thumb {
      height: 230px;
      border-radius: 10px;
      background: #061226;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-bottom: 10px
    }

    .thumb img {
      width: 100%;
      height: 100%;
      object-fit: contain; /* 🔥 IMPORTANTE */
      background: #000;
    }

    .h3 {
      font-size: 1rem;
      margin: 0 0 6px 0
    }

    .row {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      font-size: 0.95rem;
      color: var(--muted);
      margin-bottom: 6px
    }

    .price {
      font-weight: 700;
      color: var(--accent)
    }

    .badge {
      background: #08212b;
      padding: 6px 8px;
      border-radius: 8px;
      font-size: 0.85rem
    }

    .empty {
      text-align: center;
      color: var(--muted);
      padding: 40px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.02)
    }

    .footer {
      margin-top: 18px;
      text-align: center;
      color: var(--muted);
      font-size: 0.9rem
    }

    /* --- CARRITO LATERAL --- */

    .cart-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #6ee7b7;
      color: black;
      font-weight: 700;
      border: none;
      padding: 10px 16px;
      border-radius: 10px;
      cursor: pointer;
      z-index: 2000;
      font-size: 16px;
    }

    .carrito-lateral {
      position: fixed;
      top: 0;
      right: -350px;
      width: 350px;
      height: 100%;
      background: #0b1220;
      padding: 20px;
      box-shadow: -4px 0 20px rgba(0, 0, 0, .4);
      transition: right .3s ease;
      z-index: 1999;
      overflow-y: auto;
    }

    .carrito-lateral.abierto {
      right: 0;
    }

    .btn-finalizar {
      background: #6ee7b7;
      border: none;
      padding: 10px;
      border-radius: 10px;
      color: black;
      font-weight: 700;
      cursor: pointer;
      margin-top: 20px;
    }
  </style>
</head>

<body>

  <button id="btnCarrito" class="cart-toggle">
    🛒 Carrito <span id="cartCountSide">0</span>
  </button>

  <div id="carritoLateral" class="carrito-lateral">
    <h2>Tu Carrito</h2>
    <ul id="carritoLista"></ul>
    <p id="totalCarrito" style="font-weight:bold;margin-top:10px"></p>
    <button class="btn-finalizar" onclick="finalizarPedido()">Finalizar Pedido</button>
  </div>

  <div class="container">
    <div class="header">
      <div>
        <div class="title">🎮 Catálogo — Juegos</div>
        <div style="color:var(--muted);font-size:0.9rem">Tu catálogo confiable de videojuegos</div>
      </div>

      <div class="controls">
        <input id="search" class="search" placeholder="Buscar título, plataforma..." />
        <select id="filterPlatform" class="select">
          <option value="">Todas las plataformas</option>
          <option>PC</option>
          <option>PlayStation 5</option>
          <option>PlayStation 4</option>
          <option>Xbox Series X</option>
          <option>Nintendo Switch</option>
        </select>
      </div>
    </div>

    <div id="grid" class="grid"></div>
    <div id="empty" class="empty" style="display:none">No se encontraron juegos.</div>

    <div class="footer">
      Coloca las imágenes en <code>images/{id_videojuego}.jpg</code>.
    </div>
  </div>

  <script>
    const API = "../php/obtener_videojuegos.php";

    let cart = JSON.parse(localStorage.getItem("cart") || "[]");

    function saveCart() {
      localStorage.setItem("cart", JSON.stringify(cart));
      updateCartCount();
    }

    function updateCartCount() {
      const count = cart.reduce((a, b) => a + b.cantidad, 0);
      document.getElementById("cartCountSide").innerText = count;
    }

    const grid = document.getElementById('grid');
    const empty = document.getElementById('empty');
    const searchInput = document.getElementById('search');
    const filterPlatform = document.getElementById('filterPlatform');

    let allGames = [];

    async function loadGames(q = '') {
      const url = API + (q ? '?q=' + encodeURIComponent(q) : '');
      try {
        const res = await fetch(url);
        const data = await res.json();
        allGames = data.data ?? data;
        render();
      } catch {
        grid.innerHTML = '<div class="empty">Error cargando juegos.</div>';
      }
    }

    function addToCart(game) {
      const exists = cart.find(g => g.id === game.id);

      if (exists) {
        if (exists.cantidad < game.existencia) {
          exists.cantidad++;
        } else {
          alert("No hay más stock disponible.");
          return;
        }
      } else {
        cart.push({
          id: game.id,
          titulo: game.titulo,
          precio: game.precio,
          cantidad: 1
        });
      }

      saveCart();
      renderCarritoLateral();
    }

    function render() {
      const q = searchInput.value.trim().toLowerCase();
      const platform = filterPlatform.value;

      const filtered = allGames.filter(g => {
        if (platform && g.plataforma !== platform) return false;
        if (!q) return true;
        return (
          g.titulo.toLowerCase().includes(q) ||
          (g.descripcion || '').toLowerCase().includes(q) ||
          g.plataforma.toLowerCase().includes(q)
        );
      });

      if (filtered.length === 0) {
        grid.innerHTML = '';
        empty.style.display = 'block';
        return;
      }

      empty.style.display = 'none';

      grid.innerHTML = filtered.map(g => {
        const imgPath = `images/${g.id_videojuego}.jpg`;
        return `
      <div class="card">
        <div class="thumb">
          <img src="${imgPath}" alt="${g.titulo}"
            onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,${encodeURIComponent(defaultSVG())}'" />
        </div>

        <h3 class="h3">${g.titulo}</h3>

        <div class="row">
          <div class="badge">${g.plataforma}</div>
          <div class="price">$ ${Number(g.precio).toFixed(2)}</div>
        </div>

        <div style="color:var(--muted);font-size:0.95rem;margin-bottom:8px">
          ${g.descripcion || ''}
        </div>

        <div style="font-size:0.9rem;color:var(--muted);margin-bottom:6px">
          Stock: <strong>${g.existencia}</strong>
        </div>

        <button onclick='addToCart({
            id:${g.id_videojuego},
            titulo:"${g.titulo.replace(/"/g, '&quot;')}",
            precio:${g.precio},
            existencia:${g.existencia}
        })'
           style="width:100%;padding:8px;border:0;background:#6ee7b7;color:black;
                  border-radius:8px;font-weight:600;cursor:pointer;">
            Añadir al carrito 🛒
        </button>
      </div>`;
      }).join('');
    }

    function defaultSVG() {
      return `<svg xmlns='http://www.w3.org/2000/svg' width='600' height='400'>
        <rect width='100%' height='100%' fill='%23061226'/>
        <text x='50%' y='50%' fill='%239aa4b2' font-size='20'
          text-anchor='middle' dominant-baseline='middle'>
          Imagen no disponible
        </text></svg>`;
    }

    let debounce;
    searchInput.addEventListener('input', () => {
      clearTimeout(debounce);
      debounce = setTimeout(() => loadGames(searchInput.value), 300);
    });

    filterPlatform.addEventListener('change', render);

    loadGames();
    updateCartCount();

    document.getElementById("btnCarrito").addEventListener("click", () => {
      document.getElementById("carritoLateral").classList.toggle("abierto");
      renderCarritoLateral();
    });

    function renderCarritoLateral() {
      const lista = document.getElementById("carritoLista");
      const totalText = document.getElementById("totalCarrito");

      lista.innerHTML = "";
      let total = 0;

      cart.forEach(item => {
        total += item.precio * item.cantidad;

        const li = document.createElement("li");
        li.style.marginBottom = "12px";
        li.innerHTML = `
            <strong>${item.titulo}</strong><br>
            Cantidad: ${item.cantidad}<br>
            Precio: $${item.precio * item.cantidad}<br>
            <button style="margin-top:6px;padding:6px;border:none;background:#ff5f5f;
            border-radius:6px;color:white" onclick="removeItem(${item.id})">
                Eliminar
            </button>
        `;
        lista.appendChild(li);
      });

      totalText.textContent = `Total: $${total}`;
      document.getElementById("cartCountSide").innerText =
        cart.reduce((a, b) => a + b.cantidad, 0);
    }

    function removeItem(id) {
      cart = cart.filter(p => p.id !== id);
      saveCart();
      renderCarritoLateral();
    }

    async function finalizarPedido() {
      if (cart.length === 0) {
        alert("Tu carrito está vacío.");
        return;
      }

      const total = cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

      const res = await fetch("../php/insertar_pedidos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          total: total,
          items: cart
        })
      });

      const data = await res.json();

      if (data.success) {
        alert("Pedido realizado correctamente!");
        cart = [];
        saveCart();
        window.location.href = "ticket.php?id=" + data.id_pedido;
      } else {
        alert("Error al procesar el pedido: " + data.error);
      }
    }

  </script>

</body>
</html>
