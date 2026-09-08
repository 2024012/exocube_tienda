<?php
require_once 'conexion.php';
session_start();

// Validar seguridad de acceso para evitar ingresos intrusos
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: admin_login.php");
    exit;
}

try {
    // -------------------------------------------------------------------------
    // CÁLCULO DE MÉTRICAS EN TIEMPO REAL
    // -------------------------------------------------------------------------
    
    // 1. Ventas Totales (Suma de pedidos)
    $stmt_sales = $pdo->query("SELECT COALESCE(SUM(total_compra), 0.00) FROM pedidos");
    $total_ventas = $stmt_sales->fetchColumn();

    // 2. Gastos ficticios o fijos simulados (basado en gastos.html)
    $total_gastos = 6420.00;
    $ingresos_netos = $total_ventas - $total_gastos;

    // 3. Conteo de stock de juguetes directamente desde la base de datos
    // Total de productos activos en catálogo
    $stmt_tot_prod = $pdo->query("SELECT COUNT(*) FROM productos WHERE disponibilidad = 1");
    $total_productos = $stmt_tot_prod->fetchColumn();

    // Stock Bajo (Existencias menores o iguales a 5 unidades)
    $stmt_low_stock = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= 5 AND stock > 0 AND disponibilidad = 1");
    $stock_bajo = $stmt_low_stock->fetchColumn();

    // Sin Stock / Agotados
    $stmt_out_stock = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock = 0 AND disponibilidad = 1");
    $stock_agotado = $stmt_out_stock->fetchColumn();

    // -------------------------------------------------------------------------
    // BANDEJA DE ENTRADA: Obtener las 3 consultas web dinámicas más recientes (INNER JOIN)
    // -------------------------------------------------------------------------
    $stmt_inbox = $pdo->query("
        SELECT e.correo_feedback, e.fecha_registro, d.consulta_texto, d.datos_extra 
        FROM consultas_envios e 
        INNER JOIN consultas_detalles d ON e.id_envio = d.id_envio 
        ORDER BY d.id_detalle DESC LIMIT 8
    ");
    $inbox_consultas = $stmt_inbox->fetchAll();

    // Contar total de consultas para el badge de color rojo
    $stmt_count_inbox = $pdo->query("SELECT COUNT(*) FROM consultas_detalles");
    $total_mensajes_inbox = $stmt_count_inbox->fetchColumn();

    // -------------------------------------------------------------------------
    // COMENTARIOS RECIENTES: Obtener las 3 valoraciones más recientes de clientes
    // -------------------------------------------------------------------------
    $stmt_reviews = $pdo->query("SELECT * FROM testimonios ORDER BY id_testimonio DESC LIMIT 6");
    $testimonios = $stmt_reviews->fetchAll();

} catch (Exception $e) {
    die("Error al procesar las consultas del dashboard administrativo.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Tienda Exocube Admin - Panel de Control</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
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
<body class="bg-surface text-on-surface">
<!-- Sidebar de Navegación -->
<aside class="fixed left-0 top-0 h-full w-[280px] border-r border-slate-100 bg-[#F8F9FA] flex flex-col gap-2 p-4 z-50">
   <div class="px-2 py-4 mb-4">
      <h1 class="text-xl font-black text-slate-900 tracking-tight">Tienda Exocube</h1>
      <p class="text-xs font-label-md text-slate-500 uppercase tracking-widest mt-1">Admin Backend</p>
   </div>
   <nav class="flex flex-1 flex-col space-y-2 overflow-y-auto">
  
      <a class="bg-[#B5EAD7] text-slate-800 font-bold rounded-full px-4 py-2 flex items-center gap-3 translate-x-1 duration-200" href="dashboard.php">
         <span class="material-symbols-outlined">dashboard</span>
         <span class="font-body-sm">Dashboard</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="ventas.php">
         <span class="material-symbols-outlined">payments</span>
         <span class="font-body-sm">Ventas</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="gastos.php">
         <span class="material-symbols-outlined">receipt_long</span>
         <span class="font-body-sm">Gastos</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="inventario.php">
         <span class="material-symbols-outlined">inventory_2</span>
         <span class="font-body-sm">Inventario</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="sedes_admin.php">
         <span class="material-symbols-outlined">store</span>
         <span>Sedes de la Tienda</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="empresa_admin.php">
         <span class="material-symbols-outlined">info</span>
         <span>Información de la Tienda</span>
      </a>
      <a class="text-slate-400 hover:text-[#B5EAD7] px-4 py-2 flex items-center gap-3 hover:translate-x-1 transition-transform" href="politicas_admin.php">
         <span class="material-symbols-outlined">gavel</span>
         <span>Políticas del Portal</span>
      </a>
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

<!-- Main Content -->
<main class="ml-[280px] min-h-screen">
   <!-- Top Bar -->
   <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 px-6 py-3 h-16 flex justify-between items-center shadow-sm">
      <div class="flex items-center gap-4 flex-1">
         <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
            <!-- Al dar enter, este buscador redirigirá dinámicamente al inventario para filtrar de inmediato -->
            <form action="inventario.php" method="GET">
                <input class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-full text-sm focus:ring-2 focus:ring-primary-container" name="search" placeholder="Buscar en inventario..." type="text"/>
            </form>
         </div>
      </div>
   </header>

   <div class="p-lg">
      <!-- Bento Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-container_gutter mb-lg">
         
         <!-- Tarjeta 1: Ventas -->
         <div class="bg-white p-card_padding rounded-xl shadow-sm flex justify-between items-center group hover:translate-y-[-4px] transition-transform duration-300">
            <div>
               <p class="text-label-md font-label-md text-on-surface-variant mb-1">VENTAS TOTALES</p>
               <h2 class="text-h2 font-h2 text-on-surface">S/. <?php echo number_format($total_ventas, 2); ?></h2>
               <span class="text-[10px] font-bold text-primary flex items-center gap-1 mt-2">
                  <span class="material-symbols-outlined text-[14px]">trending_up</span> Registrado en caja web
               </span>
            </div>
            <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center text-primary">
               <span class="material-symbols-outlined text-3xl">shopping_bag</span>
            </div>
         </div>

         <!-- Tarjeta 2: Agotados -->
         <div class="bg-white p-card_padding rounded-xl shadow-sm flex justify-between items-center group hover:translate-y-[-4px] transition-transform duration-300">
            <div>
               <p class="text-label-md font-label-md text-on-surface-variant mb-1">PRODUCTOS AGOTADOS</p>
               <h2 class="text-h2 font-h2 text-error"><?php echo $stock_agotado; ?> juguetes</h2>
               <span class="text-[10px] font-bold text-error flex items-center gap-1 mt-2">
                  <span class="material-symbols-outlined text-[14px]">warning</span> Requiere reposición
               </span>
            </div>
            <div class="w-14 h-14 rounded-full bg-secondary-container flex items-center justify-center text-secondary">
               <span class="material-symbols-outlined text-3xl">dangerous</span>
            </div>
         </div>

         <!-- Tarjeta 3: Stock Bajo -->
         <div class="bg-white p-card_padding rounded-xl shadow-sm flex justify-between items-center group hover:translate-y-[-4px] transition-transform duration-300">
            <div>
               <p class="text-label-md font-label-md text-on-surface-variant mb-1">STOCK CRÍTICO (&lt;= 5)</p>
               <h2 class="text-h2 font-h2 text-on-surface" style="color:#d6801d;"><?php echo $stock_bajo; ?> juguetes</h2>
               <span class="text-[10px] font-bold flex items-center gap-1 mt-2" style="color:#d6801d;">
                  <span class="material-symbols-outlined text-[14px]">warning</span> Revisar inventario físico
               </span>
            </div>
            <div class="w-14 h-14 rounded-full bg-tertiary-container flex items-center justify-center text-tertiary">
               <span class="material-symbols-outlined text-3xl">warning</span>
            </div>
         </div>

         <!-- Tarjeta 4: Activos -->
         <div class="bg-white p-card_padding rounded-xl shadow-sm flex justify-between items-center group hover:translate-y-[-4px] transition-transform duration-300">
            <div>
               <p class="text-label-md font-label-md text-on-surface-variant mb-1">PRODUCTOS ACTIVOS</p>
               <h2 class="text-h2 font-h2 text-on-surface"><?php echo $total_productos; ?> items</h2>
               <span class="text-[10px] font-bold text-primary flex items-center gap-1 mt-2">
                  <span class="material-symbols-outlined text-[14px]">check_circle</span> Catálogo al día
               </span>
            </div>
            <div class="w-14 h-14 rounded-full bg-primary-fixed flex items-center justify-center text-primary">
               <span class="material-symbols-outlined text-3xl">inventory_2</span>
            </div>
         </div>

      </div>

      <!-- Sección de Desglose Mejorado (Evita el texto recortado o aplastado) -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-container_gutter">
         
         <!-- MEJORA: VALORACIONES DE CLIENTES COMPLETAS Y HOLGADAS -->
         <div class="bg-white rounded-xl shadow border border-slate-50 flex flex-col">
            <div class="p-card_padding border-b border-slate-100 flex justify-between items-center">
               <h3 class="text-h3 font-h3">Valoraciones de Clientes</h3>
               <a href="testimonios_comentarios.php" target="_blank" class="text-primary text-label-md hover:underline font-bold">Ver página de testimonios</a>
            </div>
            
            <div class="p-card_padding flex flex-col gap-6"> <!-- Incrementado el espacio vertical -->
               <?php if (!empty($testimonios)): ?>
                   <?php foreach ($testimonios as $test): 
                       $doradas = str_repeat('★', $test['calificacion']);
                       $grises = str_repeat('☆', 5 - $test['calificacion']);
                   ?>
                       <div class="flex flex-col gap-3 p-4 hover:bg-slate-50 transition-colors rounded-xl border border-slate-100">
                          
                          <!-- Línea de Cabecera: Nombre y Estrellas -->
                          <div class="flex justify-between items-center">
                             <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-slate-500 text-lg">account_circle</span>
                                <h4 class="text-sm font-bold text-slate-800 tracking-tight uppercase"><?php echo htmlspecialchars($test['nombre_cliente']); ?></h4>
                             </div>
                             <div class="flex text-amber-500 text-lg tracking-wider" style="-webkit-text-stroke: 0.5px #7c2d12;">
                                 <?php echo $doradas . $grises; ?>
                             </div>
                          </div>
                          
                          <!-- CORRECCIÓN: Burbuja de texto sin recortes (line-clamp eliminado) y tipografía fluida -->
                          <div class="bg-white p-3 rounded-lg border-l-4 border-[#B5EAD7] shadow-inner text-sm leading-relaxed text-slate-700 italic">
                             "<?php echo htmlspecialchars($test['comentario']); ?>"
                          </div>
                          
                          <!-- Fecha al pie de la burbuja -->
                          <div class="text-right">
                             <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest"><?php echo date("d/m/Y H:i", strtotime($test['fecha_creacion'])); ?></span>
                          </div>
                          
                       </div>
                   <?php endforeach; ?>
               <?php else: ?>
                   <p class="text-center text-slate-400 p-8 text-sm">No se han registrado valoraciones en la tienda física o web.</p>
               <?php endif; ?>
            </div>
         </div>

         <!-- MEJORA: BANDEJA DE ENTRADA COMPLETA SIN TEXTO APLASTADO -->
         <div class="bg-white rounded-xl shadow border border-slate-50 flex flex-col">
            <div class="p-card_padding border-b border-slate-100 flex justify-between items-center">
               <div class="flex items-center gap-2">
                  <h3 class="text-h3 font-h3">Consultas Recibidas (Inbox)</h3>
                  <span class="bg-error text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?php echo $total_mensajes_inbox; ?></span>
               </div>
               <a href="formulario_pedido.php" target="_blank" class="text-primary text-label-md hover:underline font-bold">Ver Formulario</a>
            </div>
            
            <div class="p-card_padding flex flex-col gap-6">
               <?php if (!empty($inbox_consultas)): ?>
                   <?php foreach ($inbox_consultas as $msg): ?>
                       <div class="flex flex-col gap-2 p-4 bg-emerald-50/10 border-l-4 border-[#366758] rounded-r-xl border-y border-r border-slate-100 shadow-sm">
                          
                          <!-- Cabecera del Mensaje -->
                          <div class="flex justify-between items-center">
                             <p class="text-xs font-bold text-[#366758] font-mono select-all"><?php echo htmlspecialchars($msg['correo_feedback']); ?></p>
                             <span class="text-[10px] font-bold text-slate-400"><?php echo date("d/m/Y H:i", strtotime($msg['fecha_registro'])); ?></span>
                          </div>
                          
                          <!-- CORRECCIÓN: Contenido del ticket de consulta sin límites de una sola línea (Sólido y completo) -->
                          <div class="text-sm leading-relaxed text-slate-700 bg-white p-3 border border-slate-100 rounded-lg">
                             <?php echo nl2br(htmlspecialchars($msg['consulta_texto'])); ?>
                             
                             <?php if (!empty($msg['datos_extra'])): ?>
                                 <div class="mt-2 text-xs font-semibold text-slate-400 bg-slate-50 px-2 py-1 inline-block rounded">
                                     Nota: <?php echo htmlspecialchars($msg['datos_extra']); ?>
                                 </div>
                             <?php endif; ?>
                          </div>
                          
                       </div>
                   <?php endforeach; ?>
               <?php else: ?>
                   <p class="text-center text-slate-400 p-8 text-sm">La bandeja de entrada está vacía.</p>
               <?php endif; ?>
            </div>
         </div>

      </div>
   </div>
</main>
</body>
</html>