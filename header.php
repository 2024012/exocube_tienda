<?php
// 1. Iniciar sesión de forma segura si no ha sido iniciada antes
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Detectar dinámicamente el nombre del archivo PHP actual (ej: "inicio.php")
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<!-- Estructura Unificada de la Cabecera de exo_cube -->
<div class="encabezado" style="box-sizing: border-box;">
   
   <!-- Logotipo: Puerta de enlace secreta al login administrativo -->
   <a href="admin_login.php" target="_blank"><img src="img/tienda.png" alt="tienda" class="tienda"></a>
   
   <!-- PESTAÑA: INICIO -->
   <?php if ($pagina_actual == 'inicio.php'): ?>
       <!-- Si está en inicio.php, se muestra el texto sin enlace y deshabilitado -->
       <p class="inicio" style="pointer-events: none; opacity: 0.6; cursor: default;">INICIO</p>
   <?php else: ?>
       <p class="inicio"><a href="inicio.php" target="_blank">INICIO</a></p>
   <?php endif; ?>
   
   <!-- PESTAÑA: NOSOTROS -->
   <?php if ($pagina_actual == 'nosotros.php'): ?>
       <p class="nosotros" style="pointer-events: none; opacity: 0.6; cursor: default;">NOSOTROS</p>
   <?php else: ?>
       <p class="nosotros"><a href="nosotros.php" target="_blank">NOSOTROS</a></p>
   <?php endif; ?>
   
   <!-- PESTAÑA: CATÁLOGO -->
   <?php if ($pagina_actual == 'catalogo.php'): ?>
       <p class="catalogo" style="pointer-events: none; opacity: 0.6; cursor: default;">CATÁLOGO</p>
   <?php else: ?>
       <p class="catalogo"><a href="catalogo.php" target="_blank">CATÁLOGO</a></p>
   <?php endif; ?>
   
   <!-- PESTAÑA: CARRITO DE COMPRAS (ÍCONO) -->
   <?php if ($pagina_actual == 'carrito_compras.php'): ?>
       <img src="img/carrito-de-compras.png" alt="carrito" class="carrito" style="pointer-events: none; opacity: 0.6; cursor: default;">
   <?php else: ?>
       <a href="carrito_compras.php" target="_blank"><img src="img/carrito-de-compras.png" alt="carrito" class="carrito"></a>
   <?php endif; ?>
   
   <!-- PESTAÑA: PREGUNTAS FRECUENTES (ÍCONO) -->
   <?php if ($pagina_actual == 'preguntas_frecuentes.php'): ?>
       <img src="img/conversacion.png" alt="qna" class="qna" style="pointer-events: none; opacity: 0.6; cursor: default;">
   <?php else: ?>
       <a href="preguntas_frecuentes.php" target="_blank"><img src="img/conversacion.png" alt="qna" class="qna"></a>
   <?php endif; ?>
   
   <!-- PESTAÑA: ATENCIÓN AL CLIENTE / SOPORTE (ÍCONO) -->
   <?php if ($pagina_actual == 'atencion_cliente.php'): ?>
       <img src="img/agente-de-servicio-al-cliente.png" alt="atencion" class="atencion" style="pointer-events: none; opacity: 0.6; cursor: default;">
   <?php else: ?>
       <a href="atencion_cliente.php" target="_blank"><img src="img/agente-de-servicio-al-cliente.png" alt="atencion" class="atencion"></a>
   <?php endif; ?>
   
</div>