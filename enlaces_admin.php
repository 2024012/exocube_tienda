<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Carpeta de destino físico para los iconos de redes del sistema
$directorio_iconos = 'img/';

// -------------------------------------------------------------------------
// LÓGICA 1: INSERTAR UN NUEVO ENLACE DE INTERÉS (CON COMPROBANTE DE ARCHIVO)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_enlace'])) {
    $plataforma = trim($_POST['plataforma']);
    $enlace = trim($_POST['enlace']);
    $etiqueta = trim($_POST['etiqueta']);
    
    // Icono por defecto si falla la subida física
    $ruta_icono_db = 'img/social.png'; 

    // Procesar la subida física del ícono seleccionado en el explorador
    if (isset($_FILES['icono_contacto_file']) && $_FILES['icono_contacto_file']['error'] == UPLOAD_ERR_OK) {
        if (!is_dir($directorio_iconos)) {
            mkdir($directorio_iconos, 0755, true);
        }
        $nombre_archivo_original = basename($_FILES['icono_contacto_file']['name']);
        $ruta_completa_destino = $directorio_iconos . $nombre_archivo_original;

        if (move_uploaded_file($_FILES['icono_contacto_file']['tmp_name'], $ruta_completa_destino)) {
            $ruta_icono_db = $ruta_completa_destino; // Guarda: "img/facebook.png"
        }
    }

    if (!empty($plataforma) && !empty($enlace) && !empty($etiqueta)) {
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO enlaces_contacto (plataforma, enlace, etiqueta, icono_url, activo) VALUES (?, ?, ?, ?, 1)");
            $stmt_ins->execute([$plataforma, $enlace, $etiqueta, $ruta_icono_db]);
            echo "<script>alert('¡Enlace de contacto guardado con éxito!'); window.location='enlaces_admin.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al guardar el enlace de contacto.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 2: ACTUALIZAR UN ENLACE EXISTENTE (CON SUBIDA OPCIONAL)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_enlace'])) {
    $id_c = (int)$_POST['id_contacto'];
    $plataforma = trim($_POST['plataforma']);
    $enlace = trim($_POST['enlace']);
    $etiqueta = trim($_POST['etiqueta']);
    
    // Recuperar el icono actual por si decide no cambiarlo
    $ruta_icono_db = $_POST['icono_actual'];

    // Procesar la subida física si se selecciona un archivo nuevo
    if (isset($_FILES['icono_contacto_file']) && $_FILES['icono_contacto_file']['error'] == UPLOAD_ERR_OK) {
        if (!is_dir($directorio_iconos)) {
            mkdir($directorio_iconos, 0755, true);
        }
        $nombre_archivo_original = basename($_FILES['icono_contacto_file']['name']);
        $ruta_completa_destino = $directorio_iconos . $nombre_archivo_original;

        if (move_uploaded_file($_FILES['icono_contacto_file']['tmp_name'], $ruta_completa_destino)) {
            $ruta_icono_db = $ruta_completa_destino;
        }
    }

    if (!empty($plataforma) && !empty($enlace) && !empty($etiqueta)) {
        try {
            $stmt_upd = $pdo->prepare("UPDATE enlaces_contacto SET plataforma = ?, enlace = ?, etiqueta = ?, icono_url = ? WHERE id_contacto = ?");
            $stmt_upd->execute([$plataforma, $enlace, $etiqueta, $ruta_icono_db, $id_c]);
            echo "<script>alert('¡Enlace de contacto actualizado con éxito!'); window.location='enlaces_admin.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al actualizar el enlace de contacto.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 3: ELIMINAR UN ENLACE FÍSICO
// -------------------------------------------------------------------------
if (isset($_GET['eliminar'])) {
    $id_del = (int)$_GET['eliminar'];
    try {
        $stmt_del = $pdo->prepare("DELETE FROM enlaces_contacto WHERE id_contacto = ?");
        $stmt_del->execute([$id_del]);
        header("Location: enlaces_admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al eliminar el enlace de contacto.');</script>";
    }
}

// -------------------------------------------------------------------------
// LÓGICA 4: ALTERNAR ESTADO (Turn On / Turn Off)
// -------------------------------------------------------------------------
if (isset($_GET['toggle_activo']) && isset($_GET['id'])) {
    $id_tog = (int)$_GET['id'];
    $nuevo_estado = (int)$_GET['toggle_activo'] == 1 ? 0 : 1; 
    
    try {
        $stmt_tog = $pdo->prepare("UPDATE enlaces_contacto SET activo = ? WHERE id_contacto = ?");
        $stmt_tog->execute([$nuevo_estado, $id_tog]);
        header("Location: enlaces_admin.php");
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al alternar estado del enlace.');</script>";
    }
}

// -------------------------------------------------------------------------
// LÓGICA 5: PRECARGAR DATOS PARA EL MODAL DE EDICIÓN
// -------------------------------------------------------------------------
$enlace_editar = null;
$mostrar_modal_edicion = false;

if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $stmt_edit = $pdo->prepare("SELECT * FROM enlaces_contacto WHERE id_contacto = ?");
    $stmt_edit->execute([$edit_id]);
    $enlace_editar = $stmt_edit->fetch();
    
    if ($enlace_editar) {
        $mostrar_modal_edicion = true;
    }
}

try {
    $stmt_list = $pdo->query("SELECT * FROM enlaces_contacto ORDER BY id_contacto ASC");
    $enlaces = $stmt_list->fetchAll();

    $stmt_uniq_plat = $pdo->query("SELECT DISTINCT plataforma FROM enlaces_contacto ORDER BY plataforma ASC");
    $plataformas_existentes = $stmt_uniq_plat->fetchAll();

} catch (Exception $e) {
    die("Error al consultar la base de datos.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Enlaces de Contacto</title>
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
        let modal = document.getElementById('modal-enlace');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            modal.style.display = 'none';
        }
    }

    function toggleModalEdicion() {
        let modal = document.getElementById('modal-editar-enlace');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            window.location = 'enlaces_admin.php';
        }
    }

    // Funciones JS para abrir de forma remota los selectores ocultos
    function abrirExplorador() {
        document.getElementById('icono_file_input').click();
    }
    function abrirExploradorEdit() {
        document.getElementById('icono_file_input_edit').click();
    }

    // Escribir el nombre del archivo en el display
    function actualizarNombreIcono(input, idDisplay) {
        if (input.files && input.files[0]) {
            let nombre = input.files[0].name;
            document.getElementById(idDisplay).value = nombre;
        }
    }
</script>
<meta name="viewport" content="width=device-width, initial-scale=0.65">
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
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="politicas_admin.php"><span class="material-symbols-outlined">gavel</span><span>Políticas del Portal</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="backup_admin.php">
         <span class="material-symbols-outlined">cloud_download</span>
         <span>Copias de Seguridad</span>
      </a>
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="enlaces_admin.php"><span class="material-symbols-outlined">share</span><span>Enlaces de Interés</span></a>
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

<!-- Main Content Area -->
<main class="ml-[280px] min-h-screen">
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Enlaces de Interés y Redes</span></div>
</header>
<div class="p-container_gutter max-w-[1600px] mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div>

<p class="text-body-sm text-slate-400">Controla los canales de comunicación, links y redes sociales que se muestran en el sitio web.</p>
</div>
<button onclick="toggleModal()" class="bg-[#B5EAD7] text-slate-800 px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-sm hover:opacity-90 active:scale-95">
<span class="material-symbols-outlined">add</span> Agregar Enlace de Contacto
</button>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-slate-50/50">
<tr>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Ícono</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Plataforma</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Etiqueta (Texto Visible)</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Enlace URL</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Estado (Turn On/Off)</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50">
<?php if (!empty($enlaces)): ?>
    <?php foreach ($enlaces as $e): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-4">
            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center p-1">
                <!-- Mostramos dinámicamente la foto del icono cargada -->
                <img src="<?php echo htmlspecialchars($e['icono_url']); ?>" alt="Icon" class="w-8 h-8 object-contain">
            </div>
        </td>
        <td class="px-6 py-4 font-bold text-slate-800"><?php echo htmlspecialchars($e['plataforma']); ?></td>
        <td class="px-6 py-4 text-sm text-slate-600"><?php echo htmlspecialchars($e['etiqueta']); ?></td>
        <td class="px-6 py-4 text-sm text-primary font-mono select-all truncate max-w-xs"><?php echo htmlspecialchars($e['enlace']); ?></td>
        
        <td class="px-6 py-4 text-center">
            <a href="enlaces_admin.php?toggle_activo=<?php echo $e['activo']; ?>&id=<?php echo $e['id_contacto']; ?>" class="inline-flex items-center cursor-pointer">
                <?php if ($e['activo'] == 1): ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase">ENCENDIDO (ON)</span>
                <?php else: ?>
                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full uppercase">APAGADO (OFF)</span>
                <?php endif; ?>
            </a>
        </td>
        
        <td class="px-6 py-4 text-center">
            <div class="flex justify-center gap-3">
                <a href="enlaces_admin.php?edit_id=<?php echo $e['id_contacto']; ?>" class="text-primary hover:text-emerald-700 font-bold text-xs">[Editar]</a>
                <a href="enlaces_admin.php?eliminar=<?php echo $e['id_contacto']; ?>" onclick="return confirm('¿Está seguro de eliminar este canal de contacto?');" class="text-red-500 hover:text-red-700 font-bold text-xs">[Eliminar]</a>
            </div>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center p-8 text-slate-400">No hay enlaces de contacto registrados.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>

<!-- FORMULARIO MODAL INTERACTIVO DE AGREGAR (ENCTYPE MULTIPART ACTIVADO) -->
<div id="modal-enlace" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]" style="display:none;">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Agregar Canal de Contacto</h3>
            <button onclick="toggleModal()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <form action="enlaces_admin.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre de la Plataforma / Red</label>
                <input list="plataformas" name="plataforma" class="w-full px-4 py-2 border rounded-xl outline-none" required placeholder="Seleccione o escriba...">
                <datalist id="plataformas">
                    <?php foreach ($plataformas_existentes as $p_uniq): ?>
                        <option value="<?php echo htmlspecialchars($p_uniq['plataforma']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Enlace URL Directo (href)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="enlace" required placeholder="Ej: https://wa.me/... o mailto:...">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Etiqueta Visible (Texto en pantalla)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="etiqueta" required placeholder="Ej: 966085432 o @exo_cube">
            </div>
            
            <!-- CORRECCIÓN: Botón interactivo para abrir explorador en el modal agregar -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Ícono de la Plataforma</label>
                <div class="flex gap-2">
                    <input class="flex-1 px-4 py-2 border rounded-xl bg-slate-50 text-slate-500 outline-none text-xs" type="text" id="icono_ruta_display" readonly placeholder="Ningún archivo seleccionado" required>
                    <button type="button" onclick="abrirExplorador()" class="px-4 py-2 bg-[#B5EAD7] text-slate-800 font-bold rounded-xl text-xs hover:opacity-90 transition-all">Seleccionar Ícono</button>
                </div>
                <!-- Input de archivo oculto -->
                <input type="file" name="icono_contacto_file" id="icono_file_input" style="display:none;" onchange="actualizarNombreIcono(this, 'icono_ruta_display')" accept="image/*" required>
            </div>
            
            <button type="submit" name="agregar_enlace" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR ENLACE</button>
        </form>
    </div>
</div>


<!-- =========================================================================
     NUEVO FORMULARIO MODAL INTERACTIVO: EDITAR ENLACE (ENCTYPE MULTIPART)
     ========================================================================= -->
<?php if ($mostrar_modal_edicion && $enlace_editar): ?>
<div id="modal-editar-enlace" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Editar Canal de Contacto</h3>
            <button onclick="toggleModalEdicion()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <form action="enlaces_admin.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <!-- Llaves de resguardo ocultas -->
            <input type="hidden" name="id_contacto" value="<?php echo $enlace_editar['id_contacto']; ?>">
            <input type="hidden" name="icono_actual" value="<?php echo htmlspecialchars($enlace_editar['icono_url']); ?>">

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre de la Plataforma / Red</label>
                <input list="plataformas_edit" name="plataforma" class="w-full px-4 py-2 border rounded-xl outline-none bg-slate-50" required value="<?php echo htmlspecialchars($enlace_editar['plataforma']); ?>">
                <datalist id="plataformas_edit">
                    <?php foreach ($plataformas_existentes as $p_uniq): ?>
                        <option value="<?php echo htmlspecialchars($p_uniq['plataforma']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Enlace URL Directo (href)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="enlace" required value="<?php echo htmlspecialchars($enlace_editar['enlace']); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Etiqueta Visible (Texto en pantalla)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="etiqueta" required value="<?php echo htmlspecialchars($enlace_editar['etiqueta']); ?>">
            </div>
            
            <!-- CORRECCIÓN: Botón interactivo para abrir explorador en el modal editar -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Ícono de la Plataforma (Subir para cambiar)</label>
                <div class="flex gap-2">
                    <input class="flex-1 px-4 py-2 border rounded-xl bg-slate-50 text-slate-500 outline-none text-xs" type="text" id="icono_ruta_display_edit" readonly value="<?php echo htmlspecialchars(basename($enlace_editar['icono_url'])); ?>">
                    <button type="button" onclick="abrirExploradorEdit()" class="px-4 py-2 bg-[#B5EAD7] text-slate-800 font-bold rounded-xl text-xs hover:opacity-90 transition-all">Cambiar</button>
                </div>
                <input type="file" name="icono_contacto_file" id="icono_file_input_edit" style="display:none;" onchange="actualizarNombreIcono(this, 'icono_ruta_display_edit')" accept="image/*">
            </div>
            
            <button type="submit" name="actualizar_enlace" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR CAMBIOS</button>
        </form>
    </div>
</div>
<?php endif; ?>

</body>
</html>