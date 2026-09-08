USE exocube_db;

-- 1. Crear la tabla de políticas generales del portal
CREATE TABLE IF NOT EXISTS politicas_portal (
    id_politica INT AUTO_INCREMENT,
    nombre_politica VARCHAR(100) NOT NULL, -- Ej: "Términos de Uso"
    contenido LONGTEXT NOT NULL,            -- Cuerpo del documento en HTML
    fecha_actualizacion DATE NOT NULL,      -- Fecha de última revisión
    PRIMARY KEY (id_politica)
) ENGINE=InnoDB;

-- 2. Inserción de los Términos y Condiciones oficiales de exo_cube (ID 1)
INSERT INTO politicas_portal (id_politica, nombre_politica, contenido, fecha_actualizacion) VALUES 
(
    1, 
    'Términos y Condiciones de Uso del Portal Web', 
    'Bienvenido al sitio web de EXO_CUBE (en adelante, "el Sitio Web"). <br><br> El acceso y uso de este portal, así como la compra de productos, implica la aceptación total de los presentes Términos y Condiciones. Si no está de acuerdo con ellos, le rogamos que no utilice el sitio.<br>
    <ol>
       <li>OBJETO <br><br>
El Sitio Web tiene como objeto la comercialización minorista de juguetes y regalos (en adelante, "los Productos"). <br><br> La tienda se reserva el derecho de modificar, en cualquier momento, la presentación, configuración del sitio y los productos ofrecidos.</li><br>
              <li>USO DEL SITIO Y REGISTRO <br>
<ul><br>
              <li>Edad mínima: Al utilizar este sitio, usted declara que es mayor de edad en su país o estado de residencia.

</li><br>
<li>Cuenta de Usuario: Para realizar compras, el usuario podrá registrarse. Es responsabilidad del usuario mantener la confidencialidad de su contraseña y cuenta.</li><br>
<li>Uso prohibido: Queda prohibido el uso del sitio para fines ilícitos, envío de virus, o cualquier actividad que afecte la integridad de la plataforma. </li>
              
</ul></li><br>
<li>CONDICIONES DE VENTA Y PRECIOS <br>
<ul><br>
   <li>Precios: Todos los precios están expresados en Soles (S/.) e incluyen los impuestos aplicables de ley (IGV), a menos que se indique lo contrario.</li><br>
   <li>Modificaciones: Nos reservamos el derecho de modificar los precios en cualquier momento sin previo aviso. Sin embargo, se aplicará el precio vigente al momento de confirmar el pedido.</li><br>
   <li>Disponibilidad: Todos los pedidos están sujetos a disponibilidad de stock. Si un producto no estuviera disponible tras la compra, se informará al cliente y se procederá al reembolso total o cambio del producto.</li><br>

</ul></li>
              <li>PROCESO DE PAGO <br><br>
              El pago se realizará a través de las plataformas seguras integradas en el sitio, como Yape, Plin o transferencia bancaria virtual. También, se acepta el pago en efectivo directamente en la tienda física al seleccionar recojo. <br><br> La orden se procesará una vez que hayamos recibido la confirmación de pago de la transacción.
              <br>
<br></li>

              <li>ENVÍOS Y ENTREGAS <br><br>
            
              <ul>
                <li>Zonas de cobertura: Realizamos envíos locales en toda la ciudad de Tacna. </li><br>
                <li>Plazos: Los tiempos de entrega son estimaciones y pueden variar por causas ajenas a la tienda (logística del courier).</li><br>
                <li>Responsabilidad: La tienda no se hace responsable de retrasos causados por direcciones incorrectas proporcionadas por el usuario.</li><br>
              </ul>
</li>
<li>POLÍTICA DE CAMBIOS Y DEVOLUCIONES<br>
<ul><br>
  <li>Plazo: El cliente dispone de 1 día hábil desde la recepción del producto para solicitar un cambio en la tienda física de exo_cube.</li><br>
  <li>Condiciones: Los juguetes deben estar en perfecto estado, con sus empaques originales, etiquetas y sin señales de haber sido abiertos o usados. No se aceptan devoluciones de dinero una vez emitido el comprobante de pago.</li><br>
  <li>Costos: Los costos de traslado para cambios corren por cuenta del cliente, salvo fallas de fábrica demostradas con pruebas gráficas al vendedor.</li>
</ul><br>
Vea con más detalles, en nuestra pagina de Políticas de devoluciones. <br>
<br>
</li>

<li>PROPIEDAD INTELECTUAL <br><br>
   Todo el contenido de este sitio (textos, fotografías de productos, logotipos, gráficos, iconos, animaciones) es propiedad exclusiva de EXO_CUBE o de sus proveedores y está protegido por las leyes de propiedad intelectual internacionales y nacionales. Queda prohibida su reproducción o uso sin autorización previa por escrito. <br><br></li>

<li>DESCARGO DE RESPONSABILIDAD (FOTOGRAFÍAS) <br><br>
   Hemos hecho todo lo posible para mostrar con la mayor precisión posible los colores y texturas de nuestros productos. Sin embargo, no podemos garantizar que la pantalla de su dispositivo móvil o monitor muestre los colores de manera exacta al producto físico.
<br></li><br>

<li>PROTECCIÓN DE DATOS<br><br>
   El uso de sus datos personales se rige por nuestra Política de Privacidad, la cual cumple estrictamente con la Ley de Protección de Datos Personales vigente. <br><br>
</li>

<li>MODIFICACIONES DE LOS TÉRMINOS<br><br>
   Nos reservamos el derecho de actualizar o modificar estos Términos y Condiciones en cualquier momento sin previo aviso. El uso continuado del sitio tras dichos cambios constituye la aceptación de los nuevos términos de uso. <br><br></li>

<li>LEY APLICABLE Y JURISDICCIÓN<br><br>
   Estos términos se rigen por las leyes actuales de la legislación peruana. Cualquier controversia derivada del uso del sitio web o de la compra de productos será sometida a los tribunales competentes de la ciudad de Tacna. <br></li>
</ol>', 
    '2026-05-25'
);