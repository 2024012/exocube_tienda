<?php
require_once 'conexion.php';
session_start();

// Validar seguridad de acceso
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Lógica 1: AGREGAR una nueva política o versión legal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_politica'])) {
    $nombre = $_POST['nombre_politica']; // Captura el valor del <select> unificado
    $contenido = trim($_POST['contenido']);
    $fecha = $_POST['fecha_actualizacion'];

    if (!empty($nombre) && !empty($contenido) && !empty($fecha)) {
        try {
            // Guardamos el nuevo documento. Por defecto ingresa como borrador (activo = 0) para que el admin elija cuándo encenderlo
            $stmt_ins = $pdo->prepare("INSERT INTO politicas_portal (nombre_politica, contenido, fecha_actualizacion, activo) VALUES (?, ?, ?, 0)");
            $stmt_ins->execute([$nombre, $contenido, $fecha]);
            echo "<script>alert('¡Borrador guardado con éxito! Ahora puede encenderlo (Turn On) en la tabla cuando desee publicarlo.'); window.location='politicas_admin.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al guardar el documento legal.');</script>";
        }
    }
}

// Lógica 2: ELIMINAR una versión legal
if (isset($_GET['eliminar'])) {
    $id_del = (int)$_GET['eliminar'];
    try {
        $stmt_del = $pdo->prepare("DELETE FROM politicas_portal WHERE id_politica = ?");
        $stmt_del->execute([$id_del]);
        header("Location: politicas_admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al eliminar el documento.');</script>";
    }
}

// LÓGICA 3: INTERRUPTOR INTELIGENTE (Turn On / Turn Off)
if (isset($_GET['toggle_activo']) && isset($_GET['id'])) {
    $id_tog = (int)$_GET['id'];
    $estado_actual = (int)$_GET['toggle_activo'];
    
    try {
        // Iniciamos transacción para asegurar que la desactivación de las viejas versiones y activación de la nueva ocurra junta
        $pdo->beginTransaction();
        
        if ($estado_actual == 0) {
            // El admin desea ENCENDER (ON) esta versión
            
            // 1. Obtener la categoría/nombre de este documento específico
            $stmt_get_name = $pdo->prepare("SELECT nombre_politica FROM politicas_portal WHERE id_politica = ?");
            $stmt_get_name->execute([$id_tog]);
            $categoria_documento = $stmt_get_name->fetchColumn();
            
            // 2. APAGAR (establecer activo = 0) de inmediato todos los otros documentos que pertenezcan a esa misma categoría
            $stmt_apagar_otros = $pdo->prepare("UPDATE politicas_portal SET activo = 0 WHERE nombre_politica = ?");
            $stmt_apagar_otros->execute([$categoria_documento]);
            
            // 3. ENCENDER (establecer activo = 1) únicamente el documento seleccionado
            $stmt_encender = $pdo->prepare("UPDATE politicas_portal SET activo = 1 WHERE id_politica = ?");
            $stmt_encender->execute([$id_tog]);
            
        } else {
            // El admin desea APAGAR (OFF) esta versión (se queda sin ninguna política activa para esa categoría temporalmente)
            $stmt_apagar_uno = $pdo->prepare("UPDATE politicas_portal SET activo = 0 WHERE id_politica = ?");
            $stmt_apagar_uno->execute([$id_tog]);
        }
        
        $pdo->commit();
        header("Location: politicas_admin.php");
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error de base de datos al cambiar de versión.'); window.location='politicas_admin.php';</script>";
        exit;
    }
}

try {
    $stmt_list = $pdo->query("SELECT * FROM politicas_portal ORDER BY nombre_politica ASC, id_politica DESC");
    $politicas = $stmt_list->fetchAll();
} catch (Exception $e) {
    die("Error al consultar las políticas.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Políticas del Portal</title>
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
        let modal = document.getElementById('modal-politica');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            modal.style.display = 'none';
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
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php"><span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="empresa_admin.php"><span class="material-symbols-outlined">info</span><span>Información de la Tienda</span></a>
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="politicas_admin.php"><span class="material-symbols-outlined">gavel</span><span>Políticas del Portal</span></a>
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
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Políticas y Términos del Portal</span></div>
</header>
<div class="p-container_gutter max-w-[1600px] mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div>

<p class="text-body-sm text-slate-400">Versionador de políticas legales. Encienda (Turn On) la versión que desee publicar y automáticamente se apagarán las versiones anteriores.</p>
</div>
<button onclick="toggleModal()" class="bg-[#B5EAD7] text-slate-800 px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-sm hover:opacity-90 active:scale-95">
<span class="material-symbols-outlined">add</span> Agregar Nueva Versión de Documento Legal
</button>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-slate-50/50">
<tr>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Categoría / Tipo de Documento</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">ID Versión</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Última Modificación</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Publicación (Turn On/Off)</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50">
<?php if (!empty($politicas)): ?>
    <?php foreach ($politicas as $pol): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-4 text-sm font-bold text-slate-800"><?php echo htmlspecialchars($pol['nombre_politica']); ?></td>
        <td class="px-6 py-4 font-mono text-xs text-primary">#VER-<?php echo $pol['id_politica']; ?></td>
        <td class="px-6 py-4 text-sm text-slate-500"><?php echo date("d/m/Y", strtotime($pol['fecha_actualizacion'])); ?></td>
        
        <!-- INTERRUPTOR EXCLUSIVO INTELIGENTE (TOGGLE) -->
        <td class="px-6 py-4 text-center">
            <a href="politicas_admin.php?toggle_activo=<?php echo $pol['activo']; ?>&id=<?php echo $pol['id_politica']; ?>" class="inline-flex items-center cursor-pointer">
                <?php if ($pol['activo'] == 1): ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">PUBLICADO (ON)</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">BORRADOR (OFF)</span>
                <?php endif; ?>
            </a>
        </td>
        
        <td class="px-6 py-4 text-center">
            <a href="politicas_admin.php?eliminar=<?php echo $pol['id_politica']; ?>" onclick="return confirm('¿Está seguro de eliminar esta versión?');" class="text-red-500 hover:text-red-700 font-bold text-xs">[Eliminar]</a>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="5" class="text-center p-8 text-slate-400">No hay documentos registrados.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>

<!-- FORMULARIO MODAL INTERACTIVO CON SELECT DE CATEGORÍAS VIGENTES -->
<div id="modal-politica" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]" style="display:none;">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-2xl w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Agregar Nueva Versión Legal</h3>
            <button onclick="toggleModal()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="politicas_admin.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Seleccionar Tipo de Documento</label>
                <!-- CORRECCIÓN: Reemplazado input de texto por select con tus categorías oficiales -->
                <select name="nombre_politica" class="w-full px-4 py-2 border rounded-xl outline-none" required>
                    <option value="Términos y Condiciones de Uso del Portal Web">Términos y Condiciones de Uso del Portal Web</option>
                    <option value="Políticas de Privacidad del Portal Web">Políticas de Privacidad del Portal Web</option>
                    <option value="Políticas de Devolución y Cambios">Políticas de Devolución y Cambios</option>
                    <option value="Políticas de Cookies del Portal Web">Políticas de Cookies del Portal Web</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Fecha de Actualización / Firma</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="date" name="fecha_actualizacion" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Contenido (Escribe con formato HTML)</label>
                <textarea class="w-full px-4 py-2 border rounded-xl outline-none h-48" name="contenido" required placeholder="Escriba los lineamientos de esta versión aquí..."></textarea>
            </div>
            <button type="submit" name="agregar_politica" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR BORRADOR</button>
        </form>
    </div>
</div>

</body>
</html>