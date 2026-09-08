<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Lógica para INSERTAR un nuevo gasto desde el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_gasto'])) {
    $nombre = trim($_POST['nombre_gasto']);
    $cat = $_POST['categoria'];
    $fecha = $_POST['fecha'];
    $monto = (float)$_POST['monto'];
    $metodo = $_POST['metodo_pago'];

    if (!empty($nombre) && $monto > 0 && !empty($fecha)) {
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO gastos (nombre_gasto, categoria, fecha, monto, metodo_pago) VALUES (?, ?, ?, ?, ?)");
            $stmt_ins->execute([$nombre, $cat, $fecha, $monto, $metodo]);
            echo "<script>alert('¡Gasto registrado con éxito!'); window.location='gastos.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al registrar el gasto.');</script>";
        }
    }
}

try {
    $presupuesto_mensual = 10000.00;

    // Obtener total gastado en tiempo real desde MySQL
    $stmt_spent = $pdo->query("SELECT COALESCE(SUM(monto), 0.00) FROM gastos");
    $total_gastado = $stmt_spent->fetchColumn();

    $remanente = $presupuesto_mensual - $total_gastado;
    $porcentaje_consumido = ($total_gastado / $presupuesto_mensual) * 100;

    // Obtener desglose por categorías (Español)
    $stmt_cat_spent = $pdo->query("SELECT categoria, COALESCE(SUM(monto), 0.00) as total FROM gastos GROUP BY categoria");
    $gastos_por_categoria = $stmt_cat_spent->fetchAll(PDO::FETCH_KEY_PAIR);

    $val_inventory = $gastos_por_categoria['Inventario'] ?? 0.00;
    $val_marketing = $gastos_por_categoria['Marketing'] ?? 0.00;
    $val_logistics = $gastos_por_categoria['Logistica'] ?? 0.00;
    $val_operations = $gastos_por_categoria['Operaciones'] ?? 0.00;

    // Obtener transacciones de gastos recientes
    $stmt_list = $pdo->query("SELECT * FROM gastos ORDER BY fecha DESC");
    $gastos = $stmt_list->fetchAll();

} catch (Exception $e) {
    die("Error al procesar la gestión de gastos.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Gestión de Gastos</title>
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
<script>
    function toggleModal() {
        let modal = document.getElementById('modal-gasto');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            modal.style.display = 'none';
        }
    }
</script>
<meta name="viewport" content="width=device-width, initial-scale=0.70">
</head>
<body class="bg-background text-on-background antialiased min-h-screen">

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
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="ventas.php"><span class="material-symbols-outlined">payments</span><span>Ventas</span></a>
<!-- Active: Expenses -->
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="gastos.php"><span class="material-symbols-outlined">receipt_long</span><span>Gastos</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="inventario.php"><span class="material-symbols-outlined">inventory_2</span><span>Inventario</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php"><span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
          <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="empresa_admin.php">
         <span class="material-symbols-outlined">info</span>
         <span>Información de la Tienda</span>
      </a>
      <!-- Active: Políticas Portal -->
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

<main class="ml-[280px] px-lg pb-lg">
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">

<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Control de Gastos</span></div>
</header>
<br>
<div class="max-w-7xl mx-auto space-y-lg">
<div class="flex justify-between items-end px-6">
<div>

<p class="text-body-sm text-body-sm text-on-surface-variant text-slate-400">Visualiza y gestiona las salidas de capital de tu tienda</p>
</div>
<button onclick="toggleModal()" class="bg-[#B5EAD7] text-slate-800 font-bold py-3 px-6 rounded-xl flex items-center gap-2 shadow-sm active:translate-y-[2px] transition-all">
<span class="material-symbols-outlined">add_circle</span> Registrar Gasto
</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<div class="bg-white p-card_padding rounded-xl shadow border border-slate-50 flex items-center justify-between px-6">
<div>
<p class="text-label-md text-on-surface-variant mb-1">Presupuesto Mensual</p>
<h3 class="text-h1 font-h1 text-on-background">S/. <?php echo number_format($presupuesto_mensual, 2); ?></h3>
</div>
<div class="w-16 h-16 rounded-full bg-primary-container/30 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-3xl">account_balance_wallet</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow border border-slate-50 flex items-center justify-between px-6">
<div>
<p class="text-label-md text-on-surface-variant mb-1">Total Gastado</p>
<h3 class="text-h1 font-h1 text-secondary">S/. <?php echo number_format($total_gastado, 2); ?></h3>
<p class="text-[11px] text-secondary font-bold mt-2 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px]">trending_up</span> <?php echo number_format($porcentaje_consumido, 1); ?>% consumido
</p>
</div>
<div class="w-16 h-16 rounded-full bg-secondary-container/30 flex items-center justify-center"><span class="material-symbols-outlined text-secondary text-3xl">payments</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow border border-slate-50 flex items-center justify-between px-6">
<div>
<p class="text-label-md text-on-surface-variant mb-1">Remanente</p>
<h3 class="text-h1 font-h1 text-primary">S/. <?php echo number_format($remanente, 2); ?></h3>
<div class="w-32 h-2 bg-slate-100 rounded-full mt-3 overflow-hidden">
<div class="bg-primary-container h-full" style="width: <?php echo $remanente > 0 ? (100 - $porcentaje_consumido) : 0; ?>%"></div>
</div>
</div>
<div class="w-16 h-16 rounded-full bg-tertiary-container/30 flex items-center justify-center"><span class="material-symbols-outlined text-tertiary text-3xl">savings</span></div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-lg items-start">
<div class="lg:col-span-8 bg-white rounded-xl overflow-hidden border border-slate-50">
<div class="p-6 border-b border-slate-100 flex justify-between items-center">
<h3 class="text-h3 font-h3">Transacciones Recientes</h3>
</div>
<table class="w-full text-left">
<thead class="bg-surface-container-low">
<tr>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-wider">Gasto</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-wider">Categoría</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-wider">Fecha</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-wider">Monto</th>
<th class="px-6 py-4 text-label-sm text-on-surface-variant uppercase tracking-wider">Método</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100">
<?php if (!empty($gastos)): ?>
    <?php foreach ($gastos as $g): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-4 py-4">
        <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center"><span class="material-symbols-outlined text-sm">receipt</span></div>
        <span class="font-semibold text-sm"><?php echo htmlspecialchars($g['nombre_gasto']); ?></span>
        </div>
        </td>
        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold"><?php echo htmlspecialchars($g['categoria']); ?></span></td>
        <td class="px-6 py-4 text-sm text-slate-500"><?php echo date("d/m/Y", strtotime($g['fecha'])); ?></td>
        <td class="px-6 py-4 font-bold text-sm text-on-background">S/. <?php echo number_format($g['monto'], 2); ?></td>
        <td class="px-6 py-4">
        <div class="flex items-center gap-2 text-xs font-medium text-slate-600"><span class="material-symbols-outlined text-sm">credit_card</span> <?php echo htmlspecialchars($g['metodo_pago']); ?></div>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center p-8 text-slate-400">No se han registrado egresos.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<div class="lg:col-span-4 space-y-lg px-6">
<div class="bg-white p-card_padding rounded-xl border border-slate-50">
<h3 class="text-h3 font-h3 mb-6 px-6 py-4">Categorías de Gasto</h3>
<div class="space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-[#B5EAD7]"></div><span class="text-sm font-medium text-on-surface-variant">Inventario</span></div>
<span class="text-sm font-bold">S/. <?php echo number_format($val_inventory, 2); ?></span>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-[#E0BBE4]"></div><span class="text-sm font-medium text-on-surface-variant">Marketing</span></div>
<span class="text-sm font-bold">S/. <?php echo number_format($val_marketing, 2); ?></span>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-[#FFDFD3]"></div><span class="text-sm font-medium text-on-surface-variant">Logística</span></div>
<span class="text-sm font-bold">S/. <?php echo number_format($val_logistics, 2); ?></span>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center gap-2"><div class="w-3 h-3 rounded-full bg-[#FEE1E8]"></div><span class="text-sm font-medium text-on-surface-variant">Operaciones</span></div>
<span class="text-sm font-bold">S/. <?php echo number_format($val_operations, 2); ?></span>
</div>
</div>
</div>
</div>
</div>
</div>
</main>

<!-- FORMULARIO MODAL INTERACTIVO DE REGISTRAR GASTO -->
<div id="modal-gasto" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]" style="display:none;">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Registrar Salida de Caja</h3>
            <button onclick="toggleModal()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="gastos.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre / Concepto de Gasto</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="nombre_gasto" required placeholder="Ej: Pago de Luz local Tacna">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Monto (S/.)</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="number" step="0.01" name="monto" required placeholder="150.00">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Fecha</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="date" name="fecha" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Categoría</label>
                <select class="w-full px-4 py-2 border rounded-xl outline-none" name="categoria" required>
                    <option value="Inventario">Inventario (Compra de stock)</option>
                    <option value="Marketing">Marketing (Publicidad en redes)</option>
                    <option value="Logística">Logística (Envíos y empaques)</option>
                    <option value="Operaciones">Operaciones (Servicios y alquiler)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Método de Pago</label>
                <select class="w-full px-4 py-2 border rounded-xl outline-none" name="metodo_pago" required>
                    <option value="Tarjeta">Tarjeta de Crédito / Débito</option>
                    <option value="Transferencia">Transferencia Bancaria</option>
                    <option value="Efectivo">Efectivo directo</option>
                </select>
            </div>
            <button type="submit" name="registrar_gasto" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">REGISTRAR EGRESO</button>
        </form>
    </div>
</div>

</body>
</html>