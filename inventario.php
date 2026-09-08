<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// -------------------------------------------------------------------------
// LÓGICA 1: INSERTAR UN NUEVO PRODUCTO
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = strtoupper(trim($_POST['nombre_prod']));
    $desc = trim($_POST['descripcion']);
    $info = trim($_POST['informacion_prod']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $id_cat = (int)$_POST['id_categoria'];
    
    $ruta_img_db = 'product_images/57.png'; 

    if (isset($_FILES['imagen_producto_file']) && $_FILES['imagen_producto_file']['error'] == UPLOAD_ERR_OK) {
        $directorio_destino = 'product_images/';
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }
        $nombre_archivo_original = basename($_FILES['imagen_producto_file']['name']);
        $ruta_completa_destino = $directorio_destino . $nombre_archivo_original;

        if (move_uploaded_file($_FILES['imagen_producto_file']['tmp_name'], $ruta_completa_destino)) {
            $ruta_img_db = $ruta_completa_destino;
        }
    }

    if (!empty($nombre) && $precio >= 0 && $stock >= 0) {
        try {
            $stmt_ins = $pdo->prepare("
                INSERT INTO productos (nombre_prod, descripcion, informacion_prod, precio, stock, id_categoria, imagen_frontal, imagen_lateral, imagen_trasera) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_ins->execute([$nombre, $desc, $info, $precio, $stock, $id_cat, $ruta_img_db, $ruta_img_db, $ruta_img_db]);
            echo "<script>alert('¡Juguete guardado con éxito!'); window.location='inventario.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al guardar el producto.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 2: EDITAR / ACTUALIZAR UN PRODUCTO EXISTENTE (INCLUYE DESCUENTOS)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_producto'])) {
    $id_p = (int)$_POST['id_producto'];
    $nombre = strtoupper(trim($_POST['nombre_prod']));
    $desc = trim($_POST['descripcion']);
    $info = trim($_POST['informacion_prod']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $descuento = (int)$_POST['descuento']; // CAPTURA DE DESCUENTO
    $id_cat = (int)$_POST['id_categoria'];
    
    // Mantener la imagen actual por defecto si no se sube una nueva
    $ruta_img_db = $_POST['imagen_actual'];

    if (isset($_FILES['imagen_producto_file']) && $_FILES['imagen_producto_file']['error'] == UPLOAD_ERR_OK) {
        $directorio_destino = 'product_images/';
        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }
        $nombre_archivo_original = basename($_FILES['imagen_producto_file']['name']);
        $ruta_completa_destino = $directorio_destino . $nombre_archivo_original;

        if (move_uploaded_file($_FILES['imagen_producto_file']['tmp_name'], $ruta_completa_destino)) {
            $ruta_img_db = $ruta_completa_destino;
        }
    }

    if (!empty($nombre) && $precio >= 0 && $stock >= 0 && $descuento >= 0 && $descuento <= 100) {
        try {
            $stmt_upd = $pdo->prepare("
                UPDATE productos 
                SET nombre_prod = ?, descripcion = ?, informacion_prod = ?, precio = ?, descuento = ?, stock = ?, id_categoria = ?, imagen_frontal = ?, imagen_lateral = ?, imagen_trasera = ? 
                WHERE id_producto = ?
            ");
            $stmt_upd->execute([$nombre, $desc, $info, $precio, $descuento, $stock, $id_cat, $ruta_img_db, $ruta_img_db, $ruta_img_db, $id_p]);
            echo "<script>alert('¡Juguete actualizado con éxito!'); window.location='inventario.php';</script>";
            exit;
        } catch (Exception $e) {
            echo "<script>alert('Error al actualizar el producto.');</script>";
        }
    }
}

// -------------------------------------------------------------------------
// LÓGICA 3: ELIMINAR UN PRODUCTO DEL INVENTARIO
// -------------------------------------------------------------------------
if (isset($_GET['delete_id'])) {
    $id_del = (int)$_GET['delete_id'];
    try {
        $stmt_del = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt_del->execute([$id_del]);
        echo "<script>alert('¡Producto eliminado del inventario!'); window.location='inventario.php';</script>";
        exit;
    } catch (Exception $e) {
        // Captura el error si el producto ya fue comprado por alguien (integridad referencial FK)
        echo "<script>alert('No se puede eliminar este producto porque está asociado a boletas de pago generadas anteriormente.'); window.location='inventario.php';</script>";
        exit;
    }
}

// -------------------------------------------------------------------------
// LÓGICA 4: PRECARGAR DATOS PARA EL MODAL DE EDICIÓN (VÍA URL GET)
// -------------------------------------------------------------------------
$producto_editar = null;
$mostrar_modal_edicion = false;

if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $stmt_edit = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt_edit->execute([$edit_id]);
    $producto_editar = $stmt_edit->fetch();
    
    if ($producto_editar) {
        $mostrar_modal_edicion = true; // Gatilla la apertura automática del modal por CSS/PHP
    }
}

// Lógica del Buscador de la barra superior
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_sql = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where_sql .= " AND p.nombre_prod LIKE ?";
    $params[] = "%" . $search . "%";
}

try {
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre_cat ASC");
    $categorias = $stmt_cats->fetchAll();

    // Contadores de stock unificados
    $stmt_tot = $pdo->query("SELECT COUNT(*) FROM productos");
    $total_productos = $stmt_tot->fetchColumn();

    $stmt_low = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= 5 AND stock > 0");
    $stock_bajo = $stmt_low->fetchColumn();

    $stmt_out = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock = 0");
    $stock_agotado = $stmt_out->fetchColumn();

    // Catálogo total
    $stmt_list = $pdo->prepare("
        SELECT p.*, c.nombre_cat 
        FROM productos p 
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria 
        $where_sql
        ORDER BY p.id_producto DESC
    ");
    $stmt_list->execute($params);
    $productos = $stmt_list->fetchAll();

} catch (Exception $e) {
    die("Error al procesar el inventario físico.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Inventario de Juguetes</title>
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
        let modal = document.getElementById('modal-producto');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            modal.style.display = 'none';
        }
    }

    function toggleModalEdicion() {
        let modal = document.getElementById('modal-editar-producto');
        if (modal.style.display === 'none' || modal.style.display === '') {
            modal.style.display = 'flex';
        } else {
            // Si cerramos el modal, limpiamos el parámetro edit_id de la URL para no dejarlo abierto al recargar
            window.location = 'inventario.php';
        }
    }

    function abrirExplorador() {
        document.getElementById('imagen_file_input').click();
    }
    function abrirExploradorEdit() {
        document.getElementById('imagen_file_input_edit').click();
    }

    function actualizarNombreDeImagen(input, id_display) {
        if (input.files && input.files[0]) {
            let nombre = input.files[0].name;
            document.getElementById(id_display).value = nombre;
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
<nav class="flex-1 space-y-2">
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="dashboard.php"><span class="material-symbols-outlined">dashboard</span><span>Dashboard</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="ventas.php"><span class="material-symbols-outlined">payments</span><span>Ventas</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="gastos.php"><span class="material-symbols-outlined">receipt_long</span><span>Gastos</span></a>
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="inventario.php"><span class="material-symbols-outlined">inventory_2</span><span>Inventario</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php"><span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="empresa_admin.php">
         <span class="material-symbols-outlined">info</span>
         <span>Información de la Tienda</span></a>
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
<main class="ml-[280px] min-h-screen">

<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Inventario</span></div>
</header>
<div class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">
<div class="flex items-center flex-1">
   <form action="inventario.php" method="GET" class="relative w-full max-w-md">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
      <input class="w-full bg-surface-container-low border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-[#B5EAD7]" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar juguetes por nombre..." type="text"/>
   </form>
</div>
</div>

<div class="p-container_gutter max-w-[1600px] mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">

<p class="text-body-sm text-slate-400 px-6">Gestiona los productos disponibles en la tienda</p>
<button onclick="toggleModal()" class="bg-[#B5EAD7] text-slate-800 px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow-sm hover:opacity-90 active:scale-95">
<span class="material-symbols-outlined">add</span> Agregar Producto
</button>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-lg mb-lg">
<div class="bg-white p-card_padding rounded-xl shadow flex items-center justify-between">
<div>
<p class="text-label-md text-slate-400 mb-1">Total Productos</p>
<h3 class="text-h1 font-h1 text-slate-800"><?php echo $total_productos; ?></h3>
</div>
<div class="w-14 h-14 bg-primary-container/20 rounded-full flex items-center justify-center text-primary"><span class="material-symbols-outlined text-3xl">inventory</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow flex items-center justify-between">
<div>
<p class="text-label-md text-slate-400 mb-1">Stock Bajo (&lt;= 5)</p>
<h3 class="text-h1 font-h1 text-slate-800 text-secondary"><?php echo $stock_bajo; ?></h3>
</div>
<div class="w-14 h-14 bg-secondary-container/20 rounded-full flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-3xl">warning</span></div>
</div>
<div class="bg-white p-card_padding rounded-xl shadow flex items-center justify-between">
<div>
<p class="text-label-md text-slate-400 mb-1">Productos Agotados</p>
<h3 class="text-h1 font-h1 text-error"><?php echo $stock_agotado; ?></h3>
</div>
<div class="w-14 h-14 bg-error-container/20 rounded-full flex items-center justify-center text-error"><span class="material-symbols-outlined text-3xl">dangerous</span></div>
</div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-slate-50/50">
<tr>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Producto</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Categoría</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-right">Precio Lista</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Descuento (%)</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Nivel de Stock</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50">
<?php if (!empty($productos)): ?>
    <?php foreach ($productos as $p): 
        if ($p['stock'] == 0) {
            $estado = "Agotado"; $color_txt = "text-red-500"; $color_bar = "bg-red-500"; $porcentaje_bar = 0;
        } elseif ($p['stock'] <= 5) {
            $estado = "Bajo Stock"; $color_txt = "text-orange-500"; $color_bar = "bg-orange-500"; $porcentaje_bar = 25;
        } else {
            $estado = "En Stock"; $color_txt = "text-emerald-500"; $color_bar = "bg-emerald-500"; $porcentaje_bar = 85;
        }
        $img = !empty($p['imagen_frontal']) ? $p['imagen_frontal'] : 'product_images/57.png';
    ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-4">
        <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100">
            <img class="w-full h-full object-cover" src="<?php echo htmlspecialchars($img); ?>"/>
        </div>
        <span class="font-body-md font-bold text-slate-800"><?php echo htmlspecialchars($p['nombre_prod']); ?></span>
        </div>
        </td>
        <td class="px-6 py-4"><span class="px-3 py-1 rounded-full bg-primary-container/30 text-on-primary-container text-xs font-bold"><?php echo htmlspecialchars($p['nombre_cat']); ?></span></td>
        <td class="px-6 py-4 text-right font-bold text-slate-800">S/. <?php echo number_format($p['precio'], 2); ?></td>
        
        <!-- Mostrar descuento registrado -->
        <td class="px-6 py-4 text-center font-bold text-red-500">
            <?php echo $p['descuento'] > 0 ? $p['descuento'] . '%' : '-'; ?>
        </td>

        <td class="px-6 py-4">
        <div class="flex flex-col gap-1.5" style="width:150px;">
        <div class="flex justify-between items-center text-[10px] font-bold">
        <span class="<?php echo $color_txt; ?>"><?php echo $estado; ?></span>
        <span class="text-slate-400"><?php echo $p['stock']; ?> unidades</span>
        </div>
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
        <div class="h-full <?php echo $color_bar; ?>" style="width: <?php echo $porcentaje_bar; ?>%"></div>
        </div>
        </div>
        </td>
        
        <!-- ACCIONES COMPLETAMENTE FUNCIONALES: EDITAR Y ELIMINAR -->
        <td class="px-6 py-4">
            <div class="flex justify-center gap-2">
                <!-- Abre el modal de edición mediante URL GET -->
                <a href="inventario.php?edit_id=<?php echo $p['id_producto']; ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-primary hover:bg-primary-container/10 transition-all">
                    <span class="material-symbols-outlined text-lg">edit</span>
                </a>
                <!-- Enlace de borrado físico del registro -->
                <a href="inventario.php?delete_id=<?php echo $p['id_producto']; ?>" onclick="return confirm('¿Está seguro de eliminar este producto del inventario de forma permanente?');" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-error hover:bg-error-container/10 transition-all">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </a>
            </div>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center p-8 text-slate-400">No se encontraron productos en el inventario.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>

<!-- FORMULARIO MODAL INTERACTIVO DE AGREGAR PRODUCTO -->
<div id="modal-producto" class="fixed inset-0 bg-black/50 items-center justify-center z-[9999]" style="display:none;">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Agregar Nuevo Producto</h3>
            <button onclick="toggleModal()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <form action="inventario.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre del Producto</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="nombre_prod" required placeholder="Ej: PISTOLA DE AGUA">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Precio (S/.)</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="number" step="0.01" name="precio" required placeholder="18.00">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Stock Inicial</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="number" name="stock" required placeholder="25">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Categoría</label>
                <select class="w-full px-4 py-2 border rounded-xl outline-none" name="id_categoria" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre_cat']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Imagen del Producto</label>
                <div class="flex gap-2">
                    <input class="flex-1 px-4 py-2 border rounded-xl bg-slate-50 text-slate-500 outline-none" type="text" id="imagen_ruta_display" readonly placeholder="Ningún archivo seleccionado" required>
                    <button type="button" onclick="abrirExplorador()" class="px-4 py-2 bg-[#B5EAD7] text-slate-800 font-bold rounded-xl text-xs hover:opacity-90 transition-all">Seleccionar Imagen</button>
                </div>
                <input type="file" name="imagen_producto_file" id="imagen_file_input" style="display:none;" onchange="actualizarNombreDeImagen(this, 'imagen_ruta_display')" accept="image/*" required>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Reseña Breve (Descripción)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="descripcion" placeholder="Pistola de agua con tanque de recarga rápida...">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Información Detallada (Ficha)</label>
                <textarea class="w-full px-4 py-2 border rounded-xl outline-none h-20 resize-none" name="informacion_prod" placeholder="Fabricado en plástico ABS..."></textarea>
            </div>
            <button type="submit" name="agregar_producto" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR INFORMACION DEL PRODUCTO</button>
        </form>
    </div>
</div>


<!-- =========================================================================
     NUEVO FORMULARIO MODAL INTERACTIVO: EDITAR PRODUCTO (CON DESCUENTO)
     ========================================================================= -->
<?php if ($mostrar_modal_edicion && $producto_editar): ?>
<div id="modal-editar-producto" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg w-full border-2 border-slate-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Editar Producto y Descuento</h3>
            <button onclick="toggleModalEdicion()" class="text-2xl text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        
        <form action="inventario.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <!-- Campos ocultos de resguardo -->
            <input type="hidden" name="id_producto" value="<?php echo $producto_editar['id_producto']; ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto_editar['imagen_frontal']); ?>">

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Nombre del Producto</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none bg-slate-50" type="text" name="nombre_prod" required value="<?php echo htmlspecialchars($producto_editar['nombre_prod']); ?>">
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Precio (S/.)</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="number" step="0.01" name="precio" required value="<?php echo htmlspecialchars($producto_editar['precio']); ?>">
                </div>
                <!-- NUEVO CAMPO ADICIONAL: DESCUENTO EN PORCENTAJE (0-100) -->
                <div>
                    <label class="block text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Descuento (%)</label>
                    <input class="w-full px-4 py-2 border border-red-300 rounded-xl outline-none focus:ring-2 focus:ring-red-100" type="number" min="0" max="99" name="descuento" required value="<?php echo htmlspecialchars($producto_editar['descuento']); ?>">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Stock físico</label>
                    <input class="w-full px-4 py-2 border rounded-xl outline-none" type="number" name="stock" required value="<?php echo htmlspecialchars($producto_editar['stock']); ?>">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Categoría</label>
                <select class="w-full px-4 py-2 border rounded-xl outline-none" name="id_categoria" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['id_categoria']; ?>" <?php echo $cat['id_categoria'] == $producto_editar['id_categoria'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['nombre_cat']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Imagen del Producto (Subir si desea cambiarla)</label>
                <div class="flex gap-2">
                    <input class="flex-1 px-4 py-2 border rounded-xl bg-slate-50 text-slate-500 outline-none text-xs" type="text" id="imagen_ruta_display_edit" readonly value="<?php echo htmlspecialchars(basename($producto_editar['imagen_frontal'])); ?>">
                    <button type="button" onclick="abrirExploradorEdit()" class="px-4 py-2 bg-[#B5EAD7] text-slate-800 font-bold rounded-xl text-xs hover:opacity-90 transition-all">Cambiar</button>
                </div>
                <input type="file" name="imagen_producto_file" id="imagen_file_input_edit" style="display:none;" onchange="actualizarNombreDeImagen(this, 'imagen_ruta_display_edit')" accept="image/*">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Reseña Breve (Descripción)</label>
                <input class="w-full px-4 py-2 border rounded-xl outline-none" type="text" name="descripcion" value="<?php echo htmlspecialchars($producto_editar['descripcion']); ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Información Detallada (Ficha)</label>
                <textarea class="w-full px-4 py-2 border rounded-xl outline-none h-20 resize-none" name="informacion_prod"><?php echo htmlspecialchars($producto_editar['informacion_prod']); ?></textarea>
            </div>
            <button type="submit" name="actualizar_producto" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow">GUARDAR CAMBIOS</button>
        </form>
    </div>
</div>
<?php endif; ?>

</body>
</html>