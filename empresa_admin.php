<?php
require_once 'conexion.php';
session_start();

// Validar seguridad de acceso
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$mensaje_exito = "";

// 1. Procesar la actualización de la información (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_info'])) {
    $quienes_somos = trim($_POST['quienes_somos']);
    $experiencia = trim($_POST['experiencia']);

    if (!empty($quienes_somos) && !empty($experiencia)) {
        try {
            $stmt_upd = $pdo->prepare("UPDATE informacion_tienda SET quienes_somos = ?, experiencia = ? WHERE id_info = 1");
            $stmt_upd->execute([$quienes_somos, $experiencia]);
            $mensaje_exito = "¡La información institucional de exo_cube ha sido actualizada con éxito!";
        } catch (Exception $e) {
            $mensaje_exito = "Error al actualizar los datos en el servidor.";
        }
    } else {
        $mensaje_exito = "Por favor, complete todos los campos obligatorios.";
    }
}

try {
    // 2. Consultar los datos actuales para precargarlos en el formulario
    $stmt_info = $pdo->query("SELECT * FROM informacion_tienda WHERE id_info = 1");
    $info = $stmt_info->fetch();

    if (!$info) {
        die("Error crítico: No se encontró el registro base en la base de datos.");
    }
} catch (Exception $e) {
    die("Error al consultar la información de la empresa.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Información de la Tienda</title>
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
<meta name="viewport" content="width=device-width, initial-scale=0.70">
</head>
<body class="bg-surface text-on-surface">

<!-- Sidebar de Navegación (Se añade la nueva pestaña de forma consistente) -->
<aside class="fixed left-0 top-0 h-screen w-[280px] z-50 bg-white border-r border-slate-100 flex flex-col p-6 space-y-8">
<div class="flex items-center gap-4 px-2">
<div class="w-10 h-10 rounded-xl bg-primary-container flex items-center justify-center">
<span class="material-symbols-outlined text-primary">grid_view</span>
</div>
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
<a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php">
    <span class="material-symbols-outlined">store</span><span>Sedes de la Tienda</span></a>
<!-- Active: Empresa Admin -->
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="empresa_admin.php"><span class="material-symbols-outlined">info</span><span>Información de la Tienda</span></a>
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

<!-- Main Content Area -->
<main class="ml-[280px] w-full min-h-screen">
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 shadow-sm flex justify-between items-center w-full px-6 py-3">
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Información de la Tienda</span></div>
</header>

<div class="p-lg max-w-4xl space-y-lg">
<div>
    <br>
<p class="font-body-sm text-body-sm text-on-surface-variant px-6 text-slate-400">Edita los contenidos de identidad corporativa expuestos en la página de Nosotros.</p>
</div>

<?php if (!empty($mensaje_exito)): ?>
    <div class="bg-emerald-50 border-l-4 border-[#366758] text-[#366758] p-4 rounded-r-xl shadow-sm text-sm" role="alert">
        <p class="font-bold"><?php echo $mensaje_exito; ?></p>
    </div>
<?php endif; ?>

<!-- Formulario de Edición de Información -->
<div class="bg-white p-8 rounded-2xl shadow border border-slate-50">
<form action="empresa_admin.php" method="POST" class="space-y-6">
    
    <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Sección: ¿Quiénes Somos?</label>
        <span class="block text-[11px] text-slate-400 mb-1">Este párrafo describe la historia, trayectoria y trato personalizado del negocio.</span>
        <textarea name="quienes_somos" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#B5EAD7] focus:border-[#B5EAD7] outline-none text-sm transition-all h-32 resize-none" placeholder="Redactar el texto de Quiénes Somos..."><?php echo htmlspecialchars($info['quienes_somos']); ?></textarea>
    </div>

    <div>
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Sección: ¿Qué experiencia ofrecemos?</label>
        <span class="block text-[11px] text-slate-400 mb-1">Este párrafo describe el valor de los juguetes importados y la atención al público general.</span>
        <textarea name="experiencia" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#B5EAD7] focus:border-[#B5EAD7] outline-none text-sm transition-all h-32 resize-none" placeholder="Redactar el texto de experiencia ofrecida..."><?php echo htmlspecialchars($info['experiencia']); ?></textarea>
    </div>

    <div class="pt-4 border-t border-slate-100 flex justify-end">
        <button type="submit" name="guardar_info" class="bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 px-8 rounded-xl shadow-md transition-all">
            GUARDAR CAMBIOS
        </button>
    </div>

</form>
</div>
</div>
</main>
</body>
</html>