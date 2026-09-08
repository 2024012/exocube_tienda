USE exocube_db;

-- 1. Actualizar nombres a MAYÚSCULAS para los primeros registros
UPDATE productos SET nombre_prod = 'PELOTA DE FÚTBOL' WHERE id_producto = 1;
UPDATE productos SET nombre_prod = 'FRASCO DE BURBUJAS' WHERE id_producto = 2;

-- Corregir ID 3 (BALDE DE CUBOS) con su imagen oficial (51.jpg)
UPDATE productos 
SET nombre_prod = 'BALDE DE CUBOS',
    descripcion = 'Balde clásico con bloques coloridos para construcción de diversas formas.',
    imagen_frontal = 'product_images/51.jpg',
    imagen_lateral = 'product_images/51.jpg',
    imagen_trasera = 'product_images/51.jpg'
WHERE id_producto = 3;


-- 2. Inserción de Nuevas Categorías (Evita duplicados si ya las creaste)
INSERT IGNORE INTO categorias (id_categoria, nombre_cat, descripcion) VALUES 
(3, 'TECNOLOGÍA Y GADGETS', 'Componentes electrónicos, audífonos, mouses y accesorios tecnológicos.'),
(4, 'REGALOS Y NOVEDADES', 'Termos, tazas simples, organizadores de oficina y regalos de uso diario.');


-- 3. Inserción de los Nuevos Productos con la cantidad exacta de columnas (10 campos)
INSERT INTO productos (nombre_prod, descripcion, informacion_prod, precio, stock, disponibilidad, id_categoria, imagen_frontal, imagen_lateral, imagen_trasera) VALUES

-- PELOTITAS DE GOMA (85.jpg) -> Categoría 1
(
    'PELOTITAS DE GOMA', 
    'Pelotitas de goma de alta resistencia, ideales para entretenimiento exterior.', 
    'Pack de pelotitas saltarinas de goma maciza. Colores variados de gran visibilidad, diseño ergonómico y material no tóxico.', 
    12.00, 50, 1, 1, 
    'product_images/85.jpg', 'product_images/85.jpg', 'product_images/85.jpg'
),

-- VENTILADOR PORTATIL (78.png) -> Categoría 3
(
    'VENTILADOR PORTATIL', 
    'Práctico mini ventilador recargable vía USB, ideal para escritorio o viajes.', 
    'Ventilador portátil con batería recargable integrada. Posee 3 niveles de velocidad silenciosos y base de soporte desmontable.', 
    15.00, 30, 1, 3, 
    'product_images/78.png', 'product_images/78.png', 'product_images/78.png'
),

-- PISTOLA DE AGUA (55.jpg) -> Categoría 1
(
    'PISTOLA DE AGUA', 
    'Pistola de agua con tanque de recarga rápida para juegos en exteriores.', 
    'Fabricado en plástico ABS resistente a impactos. Cuenta con un sistema de bombeo de presión manual y un alcance de chorro de hasta 5 metros.', 
    18.00, 25, 1, 1, 
    'product_images/55.jpg', 'product_images/55.jpg', 'product_images/55.jpg'
),

-- SET DE CARRITOS (54.jpg) -> Categoría 1
(
    'SET DE CARRITOS', 
    'Paquete de carritos de metal coleccionables a escala a fricción.', 
    'Set que incluye 5 vehículos metálicos de carreras de alta resistencia. Pinturas libres de plomo, ruedas de libre desplazamiento para juego directo.', 
    22.00, 15, 1, 1, 
    'product_images/54.jpg', 'product_images/54.jpg', 'product_images/54.jpg'
),

-- IMANES DE NEODIMIO (10.jpg) -> Categoría 2
(
    'IMANES DE NEODIMIO', 
    'Potentes imanes de neodimio para experimentos y manualidades.', 
    'Set de imanes de alta potencia magnética. Formato de cubos pequeños. Ideal para proyectos escolares, didácticos y modelado estructural.', 
    15.00, 40, 1, 2, 
    'product_images/10.jpg', 'product_images/10.jpg', 'product_images/10.jpg'
),

-- MUÑECA BRATZ (53.jpg) -> Categoría 1
(
    'MUÑECA BRATZ', 
    'Muñeca coleccionable con accesorios de moda de gran nivel de detalle.', 
    'Muñeca articulada de colección con sets de ropa intercambiables y accesorios temáticos de estilo urbano.', 
    45.00, 10, 1, 1, 
    'product_images/53.jpg', 'product_images/53.jpg', 'product_images/53.jpg'
),

-- KIT DE HERRAMIENTAS LEGOS (4.jpg) -> Categoría 2
(
    'KIT DE HERRAMIENTAS LEGOS', 
    'Kit didáctico de herramientas compatibles con bloques de construcción estándar.', 
    'Set de herramientas de ensamblado para bloques de construcción Lego. Ayuda a separar piezas difíciles y a facilitar el diseño de maquetas complejas.', 
    55.00, 12, 1, 2, 
    'product_images/4.jpg', 'product_images/4.jpg', 'product_images/4.jpg'
),

-- LINTERNA PORTATIL MEDIANA (5.jpg) -> Categoría 3
(
    'LINTERNA PORTATIL MEDIANA', 
    'Linterna de mano metálica con enfoque regulable y alta potencia de iluminación.', 
    'Cuerpo de aleación de aluminio de alta resistencia. Foco LED de gran alcance con tres modos de luz: Alta, Baja y Estroboscópica (S.O.S).', 
    20.00, 18, 1, 3, 
    'product_images/5.jpg', 'product_images/5.jpg', 'product_images/5.jpg'
),

-- PUNTERO LASER (6.jpg) -> Categoría 3
(
    'PUNTERO LASER', 
    'Puntero láser recargable de largo alcance, ideal para exposiciones o guías.', 
    'Puntero con haz de luz visible a grandes distancias. Cuerpo metálico con llave de seguridad de encendido. Incluye cargador y batería.', 
    15.00, 25, 1, 3, 
    'product_images/6.jpg', 'product_images/6.jpg', 'product_images/6.jpg'
),

-- SERVOMOTOR (8.jpg) -> Categoría 3
(
    'SERVOMOTOR', 
    'Servomotor de precisión para robótica, electrónica y modelado.', 
    'Servomotor de precisión compatible con Arduino y microcontroladores. Ideal para proyectos escolares y diseño de prototipos robóticos básicos.', 
    12.00, 35, 1, 3, 
    'product_images/8.jpg', 'product_images/8.jpg', 'product_images/8.jpg'
),

-- SET DE LEGOS STAR WARS (12.jpg) -> Categoría 2
(
    'SET DE LEGOS STAR WARS', 
    'Set de bloques de construcción coleccionables inspirados en Star Wars.', 
    'Kit de construcción de naves de combate de la saga Star Wars. Incluye minifiguras oficiales y guía de armado paso a paso. 120 piezas.', 
    85.00, 8, 1, 2, 
    'product_images/12.jpg', 'product_images/12.jpg', 'product_images/12.jpg'
),

-- ARDUINO UNO (13.jpg) -> Categoría 3
(
    'ARDUINO UNO', 
    'Placa de desarrollo microcontroladora estándar para proyectos de electrónica.', 
    'Placa Arduino Uno R3 original o compatible de alta durabilidad. Perfecta para estudiantes y desarrollo de prototipos electrónicos autónomos.', 
    45.00, 25, 1, 3, 
    'product_images/13.jpg', 'product_images/13.jpg', 'product_images/13.jpg'
),

-- TERMO TRANSPARENTE GRANDE (20.jpg) -> Categoría 4
(
    'TERMO TRANSPARENTE GRANDE', 
    'Termo de policarbonato resistente a impactos con capacidad de 1 Litro.', 
    'Termo para líquidos fríos con escala de medición horaria integrada. Tapa hermética antiderrames con soplador y correa de mano.', 
    30.00, 15, 1, 4, 
    'product_images/20.jpg', 'product_images/20.jpg', 'product_images/20.jpg'
),

-- SET DE TAZAS SIMPLE (21.jpg) -> Categoría 4
(
    'SET DE TAZAS SIMPLE', 
    'Set de tazas de cerámica minimalistas para el hogar o la oficina.', 
    'Par de tazas de cerámica blanca de alta temperatura con asas ergonómicas. Aptas para microondas y lavavajillas.', 
    18.00, 20, 1, 4, 
    'product_images/21.jpg', 'product_images/21.jpg', 'product_images/21.jpg'
),

-- SET DE CONDIMENTEROS (22.jpg) -> Categoría 4
(
    'SET DE CONDIMENTEROS', 
    'Organizadores de vidrio con tapas dosificadoras metálicas para cocina.', 
    'Set de 4 frascos de vidrio grueso para especias con soporte metálico giratorio para mantener la cocina ordenada.', 
    25.00, 12, 1, 4, 
    'product_images/22.jpg', 'product_images/22.jpg', 'product_images/22.jpg'
),

-- CAJA DE LAPICEROS VIKINGO (12 UNIDADES) (23.jpg) -> Categoría 4
(
    'CAJA DE LAPICEROS VIKINGO (12 UNIDADES)', 
    'Caja de bolígrafos de gel con tinta negra de flujo continuo.', 
    'Lapiceros ergonómicos de punta fina de 0.5 mm con grip de goma antideslizante para escritura prolongada de forma cómoda.', 
    20.00, 30, 1, 4, 
    'product_images/23.jpg', 'product_images/23.jpg', 'product_images/23.jpg'
),

-- AUDIFONOS GAMER (31.jpg) -> Categoría 3
(
    'AUDIFONOS GAMER', 
    'Audífonos circumaurales con micrófono omnidireccional y luces LED.', 
    'Diadema acolchada regulable con almohadillas viscoelásticas. Conexión Jack 3.5mm compatible con consolas y PC. Aislante de ruido exterior.', 
    60.00, 10, 1, 3, 
    'product_images/31.jpg', 'product_images/31.jpg', 'product_images/31.jpg'
),

-- MOUSE LOGITECH M90 BLUETOOTH (33.jpg) -> Categoría 3
(
    'MOUSE LOGITECH M90 BLUETOOTH', 
    'Mouse óptico inalámbrico de alta precisión y diseño ergonómico.', 
    'Mouse con conexión Bluetooth estable de largo alcance y sensor de seguimiento óptico de 1000 DPI para navegación fluida.', 
    38.00, 15, 1, 3, 
    'product_images/33.jpg', 'product_images/33.jpg', 'product_images/33.jpg'
);