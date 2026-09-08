<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// -------------------------------------------------------------------------
// LÓGICA 1: AGREGAR NUEVA SEDE FÍSICA (POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_sede'])) {
    $nombre = strtoupper(trim($_POST['nombre_local']));
    $direccion = trim($_POST['direccion']);
    $iframe = trim($_POST['google_maps_iframe']);
    $horario = trim($_POST['horario_atencion']);

    if (!empty($nombre) && !empty($direccion) && !empty($horario)) {
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO locales_tienda (id_info, nombre_local, direccion, google_maps_iframe, horario_atencion, activo) VALUES (1, ?, ?, ?, ?, 1)");
            $stmt_ins->execute([$nombre, $direccion, $iframe, $horario]);
            echo "<script>alert('¡Nueva sede agregada con éxito!'); window.location='sedes_admin.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al agregar la sede física.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 2: ACTUALIZAR SEDE FÍSICA EXISTENTE (POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_sede'])) {
    $id_l = (int)$_POST['id_local'];
    $nombre = strtoupper(trim($_POST['nombre_local']));
    $direccion = trim($_POST['direccion']);
    $iframe = trim($_POST['google_maps_iframe']);
    $horario = trim($_POST['horario_atencion']);

    if (!empty($nombre) && !empty($direccion) && !empty($horario)) {
        try {
            $stmt_upd = $pdo->prepare("
                UPDATE locales_tienda 
                SET nombre_local = ?, direccion = ?, google_maps_iframe = ?, horario_atencion = ? 
                WHERE id_local = ?
            ");
            $stmt_upd->execute([$nombre, $direccion, $iframe, $horario, $id_p = $id_l]);
            echo "<script>alert('¡Sucursal actualizada con éxito!'); window.location='sedes_admin.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al actualizar la sucursal.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 3: ELIMINAR SEDE FÍSICA (GET)
// -------------------------------------------------------------------------
if (isset($_GET['eliminar'])) {
    $id_del = (int)$_GET['eliminar'];
    try {
        $stmt_del = $pdo->prepare("DELETE FROM locales_tienda WHERE id_local = ?");
        $stmt_del->execute([$id_del]);
        header("Location: sedes_admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al eliminar la sede.');</script>";
    }
}

// -------------------------------------------------------------------------
// LÓGICA 4: ALTERNAR ESTADO (TOGGLE TURN ON/OFF)
// -------------------------------------------------------------------------
if (isset($_GET['toggle_activo']) && isset($_GET['id'])) {
    $id_tog = (int)$_GET['id'];
    $nuevo_estado = (int)$_GET['toggle_activo'] == 1 ? 0 : 1; 
    
    try {
        $stmt_tog = $pdo->prepare("UPDATE locales_tienda SET activo = ? WHERE id_local = ?");
        $stmt_tog->execute([$nuevo_estado, $id_tog]);
        header("Location: sedes_admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al alternar estado de la sede.');</script>";
    }
}

// -------------------------------------------------------------------------
// LÓGICA 5: PRECARGAR DATOS PARA EL MODAL DE EDICIÓN (GET)
// -------------------------------------------------------------------------
$sede_editar = null;
$mostrar_modal_edicion = false;

if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $stmt_edit = $pdo->prepare("SELECT * FROM locales_tienda WHERE id_local = ?");
    $stmt_edit->execute([$edit_id]);
    $sede_editar = $stmt_edit->fetch();
    
    if ($sede_editar) {
        $mostrar_modal_edicion = true; // Abre automáticamente el modal por CSS/PHP
    }
}

try {
    $stmt_list = $pdo->query("SELECT * FROM locales_tienda ORDER BY id_local ASC");
    $locales = $stmt_list->fetchAll();
} catch (Exception $e) {
    die("Error al consultar las sucursales.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Sedes de Tienda</title>
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
        let modal = document.getElementById('modal-sede');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            modal.style.display = 'none';
        }
    }

    function toggleModalEdicion() {
        let modal = document.getElementById('modal-editar-sede');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            // Limpia los parámetros URL para cerrar el modal limpiamente
            window.location = 'sedes_admin.php';
        }
    }
</script>
<meta name="viewport" content="width=device-width, initial-scale=0.70">
</head>
<body class="bg-surface text-on-surface">

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
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="ventas.php"><span class="material-symbols-outlined">payments</span><span>Ventas</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="gastos.php"><span class="material-symbols-outlined">receipt_long</span><span>Gastos</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="inventario.php"><span class="material-symbols-outlined">inventory_2</span><span>Inventario</span></a>
<!-- Active: Locations -->
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="sedes_admin.php"><span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
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

<main class="ml-[280px] min-h-screen">
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Sedes de Tienda</span></div>

</header>
<div class="p-container_gutter max-w-[1600px] mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div>

<p class="text-body-sm text-slate-400">Controla la información y disponibilidad horaria de tus locales físicos.</p>
</div>
<button onclick="toggleModal()" class="bg-[#B5EAD7] text-slate-800 px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-sm hover:opacity-90 active:scale-95">
<span class="material-symbols-outlined">add</span> Agregar Sede Física
</button>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-slate-50/50">
<tr>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Sede / Nombre</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Dirección</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Horario de Atención</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Estado (Turn On/Off)</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50">
<?php if (!empty($locales)): ?>
    <?php foreach ($locales as $l): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-4 font-bold text-slate-800"><?php echo htmlspecialchars($l['nombre_local']); ?></td>
        <td class="px-6 py-4 text-sm text-slate-500"><?php echo htmlspecialchars($l['direccion']); ?></td>
        <td class="px-6 py-4 text-sm text-slate-500"><?php echo htmlspecialchars($l['horario_atencion']); ?></td>
        
        <td class="px-6 py-4 text-center">
            <a href="sedes_admin.php?toggle_activo=<?php echo $l['activo']; ?>&id=<?php echo $l['id_local']; ?>" class="inline-flex items-center cursor-pointer">
                <?php if ($l['activo'] == 1): ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase">ENCENDIDO (ON)</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full uppercase">APAGADO (OFF)</span>
                <?php endif; ?>
            </a>
        </td>
        
        <!-- ACCIONES UNIFICADAS: EDITAR Y ELIMINAR -->
        <td class="px-6 py-4 text-center">
            <div class="flex justify-center gap-3">
                <!-- Gatilla la edición pasando el ID por la URL -->
                <a href="sedes_admin.php?edit_id=<?php echo $l['id_local']; ?>" class="text-primary hover:text-emerald-700 font-bold text-xs">[Editar]</a>
                <a href="sedes_admin.php?eliminar=<?php echo $l['id_local']; ?>" onclick="return confirm('¿Está seguro de eliminar esta sede física del sistema?');" class="text-red-500 hover:text-red-700 font-bold text-xs">[Eliminar]</a>
            </div>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center p-8 text-slate-400">No hay locales registrados en el sistema.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>

<!-- FORMULARIO MODAL INTERACTIVO DE AGREGAR SEDE -->
<div id="modal-sede" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]" style="display:none;">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Agregar Nueva Sucursal</h3>
            <button onclick="toggleModal()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="sedes_admin.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre Comercial de la Sede</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="nombre_local" required placeholder="Ej: SUCURSAL CONO SUR">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Dirección Exacta (Calle, Nro)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="direccion" required placeholder="Ej: Av. Municipal Nro. 456...">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Horario de Atención</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="horario_atencion" required placeholder="Ej: LUNES A SABADO 9:00 A 18:00">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Iframe de Google Maps (Código HTML)</label>
                <textarea class="w-full px-4 py-2 border rounded-xl outline-none h-20 resize-none" name="google_maps_iframe" placeholder="Pegar código iframe de Google Maps..."></textarea>
            </div>
            <button type="submit" name="agregar_sede" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR SEDE FÍSICA</button>
        </form>
    </div>
</div>


<!-- =========================================================================
     NUEVO FORMULARIO MODAL INTERACTIVO: EDITAR SUCURSAL / SEDE
     ========================================================================= -->
<?php if ($mostrar_modal_edicion && $sede_editar): ?>
<div id="modal-editar-sede" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Editar Datos de Sucursal</h3>
            <button onclick="toggleModalEdicion()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <form action="sedes_admin.php" method="POST" class="space-y-4">
            <!-- Llave de resguardo oculta -->
            <input type="hidden" name="id_local" value="<?php echo $sede_editar['id_local']; ?>">

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre Comercial de la Sede</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none bg-slate-50 font-bold" type="text" name="nombre_local" required value="<?php echo htmlspecialchars($sede_editar['nombre_local']); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Dirección Exacta (Calle, Nro)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="direccion" required value="<?php echo htmlspecialchars($sede_editar['direccion']); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Horario de Atención</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="horario_atencion" required value="<?php echo htmlspecialchars($sede_editar['horario_atencion']); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Iframe de Google Maps (Código HTML)</label>
                <textarea class="w-full px-4 py-2 border rounded-xl outline-none h-20 resize-none font-mono text-xs" name="google_maps_iframe"><?php echo htmlspecialchars($sede_editar['google_maps_iframe']); ?></textarea>
            </div>
            <button type="submit" name="actualizar_sede" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR CAMBIOS</button>
        </form>
    </div>
</div>
<?php endif; ?>

</body>
</html>