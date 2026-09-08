<?php
// Conexión auxiliar en caso de que alguna página no la haya incluido antes
require_once 'conexion.php';

try {
  
    // CORRECCIÓN: Filtramos las redes y WhatsApps agregando la condición activo = 1 (ENCENDIDO)
    
    // 1. Consultar Redes Sociales activas que estén ENCENDIDAS
    $stmt_foot_social = $pdo->query("SELECT * FROM enlaces_contacto WHERE plataforma IN ('Instagram', 'Facebook') AND activo = 1");
    $redes_sociales = $stmt_foot_social->fetchAll(PDO::FETCH_ASSOC);

    // 2. Consultar las Sucursales físicas de la tienda
    $stmt_foot_locales = $pdo->query("SELECT nombre_local FROM locales_tienda ORDER BY id_local ASC");
    $locales_tienda = $stmt_foot_locales->fetchAll(PDO::FETCH_ASSOC);

    // 2. Consultar todos los números de WhatsApp registrados que estén ENCENDIDOS
    $stmt_foot_wsp = $pdo->query("SELECT * FROM enlaces_contacto WHERE plataforma IN ('WhatsApp', 'Correo') AND activo = 1 ORDER BY id_contacto ASC");
    $telefonos_wsp = $stmt_foot_wsp->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Valores de contingencia vacíos si falla la base de datos
    $redes_sociales = [];
    $locales_tienda = [];
    $telefonos_wsp = [];
}
?>

<!-- Estructura HTML del Footer de exo_cube -->
<div class="footer" style="box-sizing: border-box;">
   
   <!-- BLOQUE 1: VISÍTANOS EN (Rotador de Redes Sociales: IG, Facebook) -->
   <div class="f01">
      <p class="visita">VISÍTANOS EN</p>
      <a id="foot_social_link" href="<?php echo htmlspecialchars($redes_sociales[0]['enlace'] ?? '#'); ?>" target="_blank" style="text-decoration: none;">
         <div class="visita02" style="justify-content: center; margin: 0 auto;">
            <img id="foot_social_img" src="<?php echo htmlspecialchars($redes_sociales[0]['icono_url'] ?? 'img/social.png'); ?>" alt="Red Social" class="visita03">
            <p id="foot_social_txt" class="visita04"><?php echo htmlspecialchars($redes_sociales[0]['etiqueta'] ?? 'exo_cube'); ?></p>
         </div>
      </a>
   </div>
   
   <!-- BLOQUE 2: SUCURSALES (Rotador de Locales Físicos) -->
   <div class="f02">
      <p class="lugar">EXOCUBE TIENDA S.A.</p>
      <p id="foot_local_txt" class="lugar02">
         <?php echo htmlspecialchars($locales_tienda[0]['nombre_local'] ?? 'Ubicación del local'); ?>
      </p>
   </div>
   
   <!-- BLOQUE 3: CONTÁCTANOS POR (Rotador de Números de WhatsApp) -->
   <div class="f03">
      <p class="contacto">CONTÁCTANOS POR</p>
      <a id="foot_wsp_link" href="<?php echo htmlspecialchars($telefonos_wsp[0]['enlace'] ?? '#'); ?>" target="_blank" style="text-decoration: none;">
         <div class="contacto02" style="justify-content: center; margin: 0 auto;">
            <img id="foot_wsp_img" src="<?php echo htmlspecialchars($telefonos_wsp[0]['icono_url'] ?? 'img/whatsapp.png'); ?>" alt="WhatsApp" class="contacto03">
            <p id="foot_wsp_txt" class="contacto04"><?php echo htmlspecialchars($telefonos_wsp[0]['etiqueta'] ?? '966085432'); ?></p>
         </div>
      </a>
   </div>
   
</div>

<!-- LÓGICA DE JAVASCRIPT DE ROTACIÓN CORREGIDA -->
<script>
    // Convertir arreglos de PHP a JSON de Javascript con validación de seguridad
    const arrSociales = <?php echo json_encode($redes_sociales); ?> || [];
    const arrLocales = <?php echo json_encode($locales_tienda); ?> || [];
    const arrTelefonos = <?php echo json_encode($telefonos_wsp); ?> || [];

    // Inicializadores de índices unificados en camelCase
    let indexSocial = 0;
    let indexLocal = 0;
    let indexWsp = 0;

    // Ejecutar la rotación cada 3 segundos (3000ms)
    setInterval(function() {
        
        // 1. Rotación del bloque de Redes Sociales (Izquierda)
        if (arrSociales.length > 1) {
            indexSocial = (indexSocial + 1) % arrSociales.length;
            let itemSocial = arrSociales[indexSocial];
            document.getElementById('foot_social_img').src = itemSocial.icono_url;
            document.getElementById('foot_social_txt').innerText = itemSocial.etiqueta;
            document.getElementById('foot_social_link').href = itemSocial.enlace;
        }

        // 2. Rotación del bloque de Sedes/Locales (Centro)
        if (arrLocales.length > 1) {
            indexLocal = (indexLocal + 1) % arrLocales.length;
            document.getElementById('foot_local_txt').innerText = arrLocales[indexLocal].nombre_local;
        }

        // 3. Rotación del bloque de WhatsApp (Derecha)
        if (arrTelefonos.length > 1) {
            indexWsp = (indexWsp + 1) % arrTelefonos.length;
            let itemWsp = arrTelefonos[indexWsp];
            document.getElementById('foot_wsp_img').src = itemWsp.icono_url;
            document.getElementById('foot_wsp_txt').innerText = itemWsp.etiqueta;
            document.getElementById('foot_wsp_link').href = itemWsp.enlace;
        }

    }, 3000); // 3 segundos de intervalo
</script>