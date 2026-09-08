USE exocube_db;

-- 1. Crear la tabla de preguntas frecuentes si no existe
CREATE TABLE IF NOT EXISTS preguntas_frecuentes (
    id_faq INT AUTO_INCREMENT,
    pregunta TEXT NOT NULL,   -- El enunciado de la pregunta
    respuesta TEXT NOT NULL,  -- La respuesta detallada
    PRIMARY KEY (id_faq)
) ENGINE=InnoDB;

-- 2. Inserción de las primeras 5 preguntas de prueba
INSERT INTO preguntas_frecuentes (pregunta, respuesta) VALUES 
(
    '¿CUÁLES SON LOS MÉTODOS DE PAGO ACEPTADOS?', 
    'Aceptamos transferencias bancarias, tarjetas de crédito/débito y billeteras digitales como Yape y Plin para procesar tus pagos de forma rápida y segura.'
),
(
    '¿OFRECEN ENVOLTURAS ESPECIALES PARA REGALO?', 
    '¡Sí, por supuesto! Al confirmar tu compra en la boleta de pago, puedes marcar la casilla "Agregar envoltura de regalo" por un costo adicional de S/. 5.00.'
),
(
    '¿HACEN ENVÍOS A DOMICILIO O TIENEN RECOJO EN TIENDA?', 
    'Ofrecemos delivery a domicilio en todo Tacna (Centro, Cono Sur, Cono Norte) y también contamos con recojo gratuito en nuestro local físico.'
),
(
    '¿CÓMO SÉ SI UN JUGUETE TIENE STOCK DISPONIBLE?', 
    'La disponibilidad se muestra en tiempo real en la ficha de cada producto y en el catálogo. Si un juguete está agotado, se marcará como no disponible.'
),
(
    '¿CUÁLES SON SUS HORARIOS DE ATENCIÓN EN LA TIENDA FÍSICA?', 
    'Nuestra tienda física en la ciudad de Tacna atiende fielmente de lunes a sábado desde las 9:00 AM hasta las 18:00 PM.'
);