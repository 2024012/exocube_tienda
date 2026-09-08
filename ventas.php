<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

try {
    // 1. Calcular métricas dinámicas
    $stmt_sales = $pdo->query("SELECT COALESCE(SUM(total_compra), 0.00) FROM pedidos");
    $total_ventas = $stmt_sales->fetchColumn();

    $stmt_avg = $pdo->query("SELECT COALESCE(AVG(total_compra), 0.00) FROM pedidos");
    $ticket_promedio = $stmt_avg->fetchColumn();

    $stmt_clients = $pdo->query("SELECT COUNT(DISTINCT cliente_correo) FROM pedidos");
    $total_clientes = $stmt_clients->fetchColumn();

    // 2. Obtener lista completa de ventas de forma descendiente
    $stmt_list = $pdo->query("SELECT * FROM pedidos ORDER BY id_pedido DESC");
    $ventas = $stmt_list->fetchAll();

} catch (Exception $e) {
    die("Error al cargar la gestión de ventas.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube - Gestión de Ventas</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "surface": "#f8f9fa",
                      "surface-container-low": "#f3f4f5",
                      "on-secondary-fixed": "#360e0d",
                      "on-background": "#191c1d",
                      "tertiary-container": "#f9d3fd",
                      "background": "#f8f9fa",
                      "on-primary-fixed": "#002018",
                      "on-primary-container": "#396b5c",
                      "outline-variant": "#c0c9c4",
                      "outline": "#707975",
                      "inverse-primary": "#9dd1bf",
                      "surface-dim": "#d9dadb",
                      "inverse-surface": "#2e3132",
                      "secondary-fixed": "#ffdad7",
                      "tertiary-fixed-dim": "#dfbbe4",
                      "primary-fixed-dim": "#9dd1bf",
                      "on-surface": "#191c1d",
                      "on-tertiary": "#ffffff",
                      "on-secondary": "#ffffff",
                      "on-error-container": "#93000a",
                      "secondary": "#874f4c",
                      "error": "#ba1a1a",
                      "primary": "#366758",
                      "on-primary": "#ffffff",
                      "surface-tint": "#366758",
                      "surface-container-high": "#e7e8e9",
                      "primary-container": "#b5ead7",
                      "on-error": "#ffffff",
                      "tertiary-fixed": "#fcd7ff",
                      "surface-container-highest": "#e1e3e4",
                      "on-surface-variant": "#404945",
                      "on-secondary-fixed-variant": "#6b3836",
                      "secondary-fixed-dim": "#fcb4b0",
                      "secondary-container": "#ffb7b2",
                      "on-tertiary-container": "#76587b",
                      "on-tertiary-fixed-variant": "#593d5f",
                      "surface-variant": "#e1e3e4",
                      "inverse-on-surface": "#f0f1f2",
                      "on-secondary-container": "#7b4542",
                      "error-container": "#ffdad6",
                      "surface-bright": "#f8f9fa",
                      "tertiary": "#725477",
                      "on-primary-fixed-variant": "#1c4f41",
                      "on-tertiary-fixed": "#2a1131",
                      "surface-container-lowest": "#ffffff",
                      "primary-fixed": "#b9eedb",
                      "surface-container": "#edeeef"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "lg": "24px",
                      "sm": "8px",
                      "card_padding": "20px",
                      "container_gutter": "24px",
                      "base": "4px",
                      "xs": "4px",
                      "sidebar_width": "280px",
                      "md": "16px",
                      "xl": "32px"
              }
            },
          },
        }
</script>
</head>
<meta name="viewport" content="width=device-width, initial-scale=0.70">
<body class="bg-background text-on-background antialiased flex">

<!-- Sidebar de Navegación -->
<aside class="fixed left-0 top-0 h-screen w-[280px] z-50 bg-white border-r border-slate-100 flex flex-col p-6 space-y-8">
<div class="flex items-center gap-4 px-2">
<div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center"><span class="material-symbols-outlined text-primary">grid_view</span></div>
<div>
<h2 class="text-xl font-black text-[#B5EAD7]">Tienda Exocube</h2>
<p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Gift Shop Admin</p>
</div>
</div>
<nav class="flex flex-1 flex-col space-y-2 overflow-y-auto">
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="dashboard.php"><span class="material-symbols-outlined">dashboard</span><span>Dashboard</span></a>
<!-- Active: Sales -->
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="ventas.php"><span class="material-symbols-outlined">analytics</span><span>Ventas</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="gastos.php"><span class="material-symbols-outlined">receipt_long</span><span>Gastos</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="inventario.php"><span class="material-symbols-outlined">inventory_2</span><span>Inventario</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php"><span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
          <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="empresa_admin.php">
         <span class="material-symbols-outlined">info</span>
         <span>Información de la Tienda</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="politicas_admin.php"><span class="material-symbols-outlined">gavel</span><span>Políticas del Portal</span></a>
<!-- Pegar este enlace en el bloque <nav> lateral de tus otros archivos PHP de administración -->
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="backup_admin.php">
         <span class="material-symbols-outlined">cloud_download</span>
         <span>Copias de Seguridad</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="enlaces_admin.php">
         <span class="material-symbols-outlined">share</span>
         <span>Enlaces de Interés</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform">
         <span class="material-symbols-outlined"></span>
         <span></span>
      </a>
    </nav>

<!-- Perfil de Administrador en Sesión -->
<div class="mt-auto p-4 bg-primary-container/30 rounded-xl">
      <div class="flex items-center gap-3">
         <div class="w-10 h-10 rounded-full overflow-hidden bg-white border-2 border-white shadow-sm flex items-center justify-center">
            <span class="material-symbols-outlined text-slate-700">admin_panel_settings</span>
         </div>
         <div>
            <p class="text-xs font-bold text-on-primary-container"><?php echo htmlspecialchars($_SESSION['admin_nombre']); ?></p>
            <p class="text-[10px] text-on-primary-container/70"><a href="admin_login.php?logout=1" class="hover:underline">Cerrar Sesión</a></p>
         </div>
      </div>
   </div>
</aside>

<!-- Main Content -->
<main class="ml-[280px] w-full min-h-screen">
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">

<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Gestión de Ventas</span></div>
</header>

<div class="p-lg space-y-lg">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-md px-6">
<div>

<p class="font-body-sm text-body-sm text-on-surface-variant text-slate-400">Historial de boletas y transacciones del portal de exo_cube</p>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<div class="bg-white p-card_padding rounded-xl shadow-sm border border-slate-50 flex justify-between items-center px-6">
<div class="space-y-base">
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Ventas Totales</p>
<h2 class="font-display text-display text-primary">S/. <?php echo number_format($total_ventas, 2); ?></h2>
</div>
<div class="w-14 h-14 bg-primary-container rounded-full flex items-center justify-center text-on-primary-container"><span class="material-symbols-outlined text-3xl">payments</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow-sm border border-slate-50 flex justify-between items-center px-6">
<div class="space-y-base">
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Ticket Promedio</p>
<h2 class="font-display text-display text-secondary">S/. <?php echo number_format($ticket_promedio, 2); ?></h2>
</div>
<div class="w-14 h-14 bg-secondary-container rounded-full flex items-center justify-center text-on-secondary-container"><span class="material-symbols-outlined text-3xl">shopping_basket</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow-sm border border-slate-50 flex justify-between items-center px-6">
<div class="space-y-base">
<p class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Clientes Únicos</p>
<h2 class="font-display text-display text-tertiary"><?php echo $total_clientes; ?></h2>
</div>
<div class="w-14 h-14 bg-tertiary-container rounded-full flex items-center justify-center text-on-tertiary-container"><span class="material-symbols-outlined text-3xl">groups</span></div>
</div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-50">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low">
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">ID Pedido</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">Cliente</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">Fecha</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">Total Boleta</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">Método Pago</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-widest">Acción</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100">
<?php if (!empty($ventas)): ?>
    <?php foreach ($ventas as $v): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-5 font-body-sm text-body-sm font-bold text-primary">#EXO-<?php echo $v['id_pedido']; ?></td>
        <td class="px-6 py-5 font-body-sm text-body-sm font-semibold"><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
        <td class="px-6 py-5 font-body-sm text-body-sm text-on-surface-variant"><?php echo date("d/m/Y H:i", strtotime($v['fecha_pedido'])); ?></td>
        <td class="px-6 py-5 font-body-sm text-body-sm font-bold">S/. <?php echo number_format($v['total_compra'], 2); ?></td>
        <td class="px-6 py-5"><span class="px-3 py-1 bg-secondary-container text-secondary text-[10px] font-black rounded-full uppercase"><?php echo htmlspecialchars($v['metodo_pago']); ?></span></td>
        <td class="px-6 py-5"><a href="imprimir_boleta.php?id_pedido=<?php echo $v['id_pedido']; ?>" target="_blank" class="text-primary hover:underline font-bold text-xs">Ver Boleta</a></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center p-8 text-slate-400">No se han registrado transacciones web todavía.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>
</body>
</html>