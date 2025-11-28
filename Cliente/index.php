<?php
// index.php
// Coloca este archivo en htdocs (XAMPP). Asegúrate de tener la extensión php-pgsql habilitada.

// ------- CONFIGURA ESTO -------
$db_host = 'localhost';
$db_port = '5432';
$db_name = 'tu_basedatos';    // cambia por el nombre real
$db_user = 'postgres';        // tu usuario
$db_pass = 'tu_contraseña';   // tu contraseña
// -------------------------------

function connect_db() {
    global $db_host, $db_port, $db_name, $db_user, $db_pass;
    $conn_str = "host={$db_host} port={$db_port} dbname={$db_name} user={$db_user} password={$db_pass}";
    $db = @pg_connect($conn_str);
    if (!$db) {
        http_response_code(500);
        echo json_encode(['error' => 'No se pudo conectar a la base de datos. Revisa credenciales y que Postgres esté corriendo.']);
        exit;
    }
    return $db;
}

// API endpoint: devuelve JSON con los videojuegos (soporta q= búsqueda)
if (isset($_GET['action']) && $_GET['action'] === 'api') {
    header('Content-Type: application/json; charset=utf-8');
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $db = connect_db();

    if ($q === '') {
        $result = pg_query($db, "SELECT id_videojuego, titulo, descripcion, precio, existencia, plataforma, fecha_ingreso FROM videojuegos ORDER BY titulo");
    } else {
        // búsqueda segura con parámetros
        $like = '%' . $q . '%';
        $result = pg_query_params($db,
            "SELECT id_videojuego, titulo, descripcion, precio, existencia, plataforma, fecha_ingreso
             FROM videojuegos
             WHERE titulo ILIKE $1 OR descripcion ILIKE $1 OR plataforma ILIKE $1
             ORDER BY titulo",
            array($like)
        );
    }

    if (!$result) {
        echo json_encode(['error' => 'Error en consulta a la base de datos.']);
        exit;
    }
    $rows = [];
    while ($r = pg_fetch_assoc($result)) $rows[] = $r;
    echo json_encode($rows);
    pg_close($db);
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Catálogo de Videojuegos</title>
<style>
/* Diseño moderno y responsivo */
:root{--bg:#0f1724;--card:#0b1220;--accent:#6ee7b7;--muted:#9aa4b2}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Segoe UI,Arial;background:linear-gradient(180deg,#071122 0%,#0f1724 100%);color:#e6eef6;padding:24px}
.container{max-width:1100px;margin:0 auto}
.header{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:18px}
.title{font-size:1.6rem;font-weight:700;letter-spacing:0.4px}
.controls{display:flex;gap:8px;align-items:center}
.search{padding:8px 12px;border-radius:10px;border:none;outline:none;width:260px;background:#0b1a2a;color:inherit}
.select{padding:8px;border-radius:10px;background:#061323;color:inherit;border:none}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px}
.card{background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(0,0,0,0.05));padding:12px;border-radius:14px;box-shadow:0 6px 18px rgba(2,6,23,0.6);transition:transform .15s}
.card:hover{transform:translateY(-6px)}
.thumb{height:140px;border-radius:10px;background:#061226;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:10px}
.thumb img{width:100%;height:100%;object-fit:cover}
.h3{font-size:1rem;margin:0 0 6px 0}
.row{display:flex;justify-content:space-between;gap:8px;font-size:0.95rem;color:var(--muted);margin-bottom:6px}
.price{font-weight:700;color:var(--accent)}
.badge{background:#08212b;padding:6px 8px;border-radius:8px;font-size:0.85rem}
.empty{text-align:center;color:var(--muted);padding:40px;border-radius:10px;background:rgba(255,255,255,0.02)}
.footer{margin-top:18px;text-align:center;color:var(--muted);font-size:0.9rem}
@media(max-width:480px){.header{flex-direction:column;align-items:flex-start}.search{width:100%}}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div>
      <div class="title">🎮 Catálogo — Juegos</div>
      <div style="color:var(--muted);font-size:0.9rem">Tu catálogo conectado a PostgreSQL</div>
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

  <div class="footer">Coloca las imágenes en <code>images/{id_videojuego}.jpg</code>. Si no existe la imagen se mostrará una miniatura por defecto.</div>
</div>

<script>
// URL del API (mismo archivo, parámetro action=api)
const API = location.pathname + '?action=api';

const grid = document.getElementById('grid');
const empty = document.getElementById('empty');
const searchInput = document.getElementById('search');
const filterPlatform = document.getElementById('filterPlatform');

let allGames = [];

async function loadGames(q='') {
  const url = API + (q ? '&q=' + encodeURIComponent(q) : '');
  try {
    const res = await fetch(url);
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    allGames = data;
    render();
  } catch (e) {
    grid.innerHTML = '<div class="empty">Error cargando juegos. Revisa la conexión y credenciales del servidor.</div>';
    console.error(e);
  }
}

function render() {
  const q = searchInput.value.trim().toLowerCase();
  const platform = filterPlatform.value;
  const filtered = allGames.filter(g => {
    if (platform && g.plataforma !== platform) return false;
    if (!q) return true;
    return (g.titulo && g.titulo.toLowerCase().includes(q))
        || (g.descripcion && g.descripcion.toLowerCase().includes(q))
        || (g.plataforma && g.plataforma.toLowerCase().includes(q));
  });

  if (filtered.length === 0) {
    grid.innerHTML = '';
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';
  grid.innerHTML = filtered.map(g => {
    const imgPath = `images/${g.id_videojuego}.jpg`;
    const price = Number(g.precio).toFixed(2);
    const existencia = Number(g.existencia);
    const date = g.fecha_ingreso ? new Date(g.fecha_ingreso).toLocaleDateString() : '';
    return `
      <div class="card">
        <div class="thumb">
          <img src="${imgPath}" alt="${escapeHtml(g.titulo)}" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,${encodeURIComponent(defaultSVG())}'" />
        </div>
        <h3 class="h3">${escapeHtml(g.titulo)}</h3>
        <div class="row">
          <div class="badge">${escapeHtml(g.plataforma||'')}</div>
          <div class="price">$ ${price}</div>
        </div>
        <div style="color:var(--muted);font-size:0.95rem;margin-bottom:8px">${escapeHtml(g.descripcion||'')}</div>
        <div style="display:flex;gap:8px;align-items:center;justify-content:space-between">
          <div style="font-size:0.9rem;color:var(--muted)">Stock: <strong>${existencia}</strong></div>
          <div style="font-size:0.85rem;color:var(--muted)">${date}</div>
        </div>
      </div>
    `;
  }).join('');
}

function escapeHtml(s){ if(!s) return ''; return s.replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;'); }

function defaultSVG(){ 
  return `<svg xmlns='http://www.w3.org/2000/svg' width='600' height='400'><rect width='100%' height='100%' fill='%23061226'/><text x='50%' y='50%' fill='%239aa4b2' font-size='20' text-anchor='middle' dominant-baseline='middle'>Imagen no disponible</text></svg>`;
}

// eventos
let debounce;
searchInput.addEventListener('input', ()=>{ clearTimeout(debounce); debounce = setTimeout(()=>{ loadGames(searchInput.value); }, 300); });
filterPlatform.addEventListener('change', render);

// carga inicial
loadGames();
</script>
</body>
</html>
