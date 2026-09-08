<?php
require_once 'conexion.php';
session_start();

// Validar seguridad de acceso
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Directorio físico de destino seguro dentro del sistema de archivos
$directorio_backups = 'backups/';

// Crear el directorio de copias si no existe en el servidor
if (!is_dir($directorio_backups)) {
    mkdir($directorio_backups, 0755, true);
    // Archivo index.html vacío para evitar listar archivos desde URL pública por seguridad
    file_put_contents($directorio_backups . 'index.html', '');
}

// -------------------------------------------------------------------------
// FUNCIÓN AUXILIAR: Conversor de tamaño de archivo (Bytes a formato legible)
// -------------------------------------------------------------------------
function formatearPesoArchivo($bytes) {
    $unidades = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($unidades) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $unidades[$pow];
}

// -------------------------------------------------------------------------
// FUNCIÓN CRÍTICA: Volcado completo de la Base de Datos a SQL nativo en PHP
// -------------------------------------------------------------------------
function ejecutarBackupPHP($pdo, $ruta_destino) {
    $sql_dump = "-- Copia de Seguridad exo_cube\n";
    $sql_dump .= "-- Generado oficialmente el: " . date("d-m-Y H:i:s") . "\n\n";
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Obtener todas las tablas relacionales de la base de datos
    $stmt_tables = $pdo->query("SHOW TABLES");
    $tablas = $stmt_tables->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tablas as $tabla) {
        // 1. Obtener estructura física de la tabla (CREATE TABLE)
        $stmt_schema = $pdo->prepare("SHOW CREATE TABLE `$tabla`");
        $stmt_schema->execute();
        $row_schema = $stmt_schema->fetch(PDO::FETCH_NUM);
        
        $sql_dump .= "DROP TABLE IF EXISTS `$tabla`;\n";
        $sql_dump .= $row_schema[1] . ";\n\n";

        // 2. Obtener registros y filas (INSERT INTO)
        $stmt_data = $pdo->query("SELECT * FROM `$tabla`");
        $filas = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

        if (count($filas) > 0) {
            foreach ($filas as $fila) {
                $columnas = array_keys($fila);
                $columnas_sql = implode('`, `', $columnas);
                
                $valores = [];
                foreach ($fila as $val) {
                    if ($val === null) {
                        $valores[] = "NULL";
                    } else {
                        $valores[] = $pdo->quote($val);
                    }
                }
                $valores_sql = implode(', ', $valores);
                $sql_dump .= "INSERT INTO `$tabla` (`$columnas_sql`) VALUES ($valores_sql);\n";
            }
            $sql_dump .= "\n";
        }
    }
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Guardar físicamente el volcado .sql en la ruta establecida
    file_put_contents($ruta_destino, $sql_dump);
}

// -------------------------------------------------------------------------
// LÓGICA DE REGISTRO DE BACKUP (DISPARADOR COMÚN)
// -------------------------------------------------------------------------
function registrarBackup($pdo, $directorio_backups, $tipo) {
    $nombre_archivo = 'exocube_db_' . date('Ymd_His') . '_' . strtolower($tipo) . '.sql';
    $ruta_completa = $directorio_backups . $nombre_archivo;
    
    // Generar el archivo .sql
    ejecutarBackupPHP($pdo, $ruta_completa);
    
    // Obtener peso físico del archivo generado
    $peso_físico = formatearPesoArchivo(filesize($ruta_completa));
    
    // Registrar el historial de respaldos en la base de datos MySQL
    $stmt_ins = $pdo->prepare("INSERT INTO backups (nombre_archivo, ruta_archivo, tipo_backup, peso_archivo) VALUES (?, ?, ?, ?)");
    $stmt_ins->execute([$nombre_archivo, $ruta_completa, $tipo, $peso_físico]);
}

// -------------------------------------------------------------------------
// LÓGICA DE GATILLO AUTOMÁTICO (PSEUDO-CRON SEMANAL - 7 DÍAS)
// -------------------------------------------------------------------------
try {
    // Buscar la fecha del último backup automático generado
    $stmt_last_auto = $pdo->query("SELECT MAX(fecha_generacion) FROM backups WHERE tipo_backup = 'Automático'");
    $ultima_fecha_auto = $stmt_last_auto->fetchColumn();

    if (!$ultima_fecha_auto || (strtotime(date('Y-m-d H:i:s')) - strtotime($ultima_fecha_auto)) >= 604800) {
        // Ha pasado más de 1 semana (604,800 segundos), gatillar backup de forma silenciosa
        registrarBackup($pdo, $directorio_backups, 'Automático');
    }
} catch (Exception $e) {
    // Ignorar fallas silenciosas del pseudo-cron para no interrumpir el flujo administrativo
}

// -------------------------------------------------------------------------
// LÓGICA DE DISPARADOR MANUAL (POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar_manual'])) {
    try {
        registrarBackup($pdo, $directorio_backups, 'Manual');
        echo "<script>alert('¡Copia de seguridad manual generada de forma exitosa!'); window.location='backup_admin.php';</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Error al generar la copia de seguridad manual.');</script>";
    }
}

try {
    // Consultar el historial completo de copias para mostrar en la tabla
    $stmt_list = $pdo->query("SELECT * FROM backups ORDER BY id_backup DESC");
    $respaldos = $stmt_list->fetchAll();
} catch (Exception $e) {
    die("Error al consultar el historial de copias de seguridad.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Resguardos de Base de Datos</title>
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
<!-- Active: Backups -->
<a class="bg-[#B5EAD7] text-slate-800 rounded-full px-4 py-2 flex items-center gap-3 shadow-sm font-bold" href="backup_admin.php"><span class="material-symbols-outlined">cloud_download</span><span>Copias de Seguridad</span></a>
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
<div class="flex items-center gap-4"><span class="text-lg font-extrabold tracking-tight text-slate-800">Tienda Exocube</span><div class="h-6 w-[1px] bg-slate-200"></div><span class="text-slate-400 text-sm font-medium">Copias de Seguridad (Backups)</span></div>
</header>
<div class="p-container_gutter max-w-[1600px] mx-auto">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div>

<p class="text-body-sm text-slate-400">Resguardos de la base de datos `exocube_db`. El sistema genera de forma automática un respaldo una vez por semana.</p>
</div>
<form action="backup_admin.php" method="POST">
   <button type="submit" name="generar_manual" class="bg-[#366758] text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 shadow hover:bg-[#2c5344] transition-all active:scale-95">
   <span class="material-symbols-outlined">cloud_upload</span> Generar Copia Manual (SQL)
   </button>
</form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
<table class="w-full text-left">
<thead class="bg-slate-50/50">
<tr>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">ID</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Nombre del Archivo .SQL</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider">Fecha de Creación</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Peso</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Tipo</th>
<th class="px-6 py-4 text-label-sm text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-50">
<?php if (!empty($respaldos)): ?>
    <?php foreach ($respaldos as $r): ?>
        <tr class="hover:bg-primary-container/5 transition-colors">
        <td class="px-6 py-4 font-bold text-slate-400">#BKP-<?php echo $r['id_backup']; ?></td>
        <td class="px-6 py-4 text-sm font-bold text-primary font-mono"><?php echo htmlspecialchars($r['nombre_archivo']); ?></td>
        <td class="px-6 py-4 text-sm text-slate-500"><?php echo date("d/m/Y H:i:s", strtotime($r['fecha_generacion'])); ?></td>
        <td class="px-6 py-4 text-sm font-semibold text-slate-600 text-center"><?php echo $r['peso_archivo']; ?></td>
        <td class="px-6 py-4 text-center">
            <?php if ($r['tipo_backup'] == 'Automático'): ?>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase">Automático</span>
            <?php else: ?>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase">Manual</span>
            <?php endif; ?>
        </td>
        <td class="px-6 py-4 text-center">
            <!-- Botón funcional para descargar directamente el archivo SQL físico a la PC -->
            <a href="<?php echo htmlspecialchars($r['ruta_archivo']); ?>" download class="text-primary hover:underline font-bold text-xs">[Descargar SQL]</a>
        </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="6" class="text-center p-8 text-slate-400">Ninguna copia de seguridad generada todavía.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</main>

</body>
</html>