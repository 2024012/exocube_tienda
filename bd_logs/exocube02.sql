-- Limpiar y corregir la información del ID 1 (Pelota de fútbol)
UPDATE productos 
SET descripcion = 'Pelota deportiva de alta resistencia, ideal para juegos al aire libre.',
    informacion_prod = 'Pelota nro 5, material de cuero sintético PVC, peso oficial, cocida a mano. Ideal para entrenamiento y recreación en superficies de césped o losa deportiva.'
WHERE id_producto = 1;

-- Limpiar y corregir la información del ID 2 (Frasco de burbujas)
UPDATE productos 
SET descripcion = 'Frascos con solución jabonosa de divertidos colores para hacer burbujas gigantes.',
    informacion_prod = 'Pack por 3 unidades de colores variados. Contiene líquido jabonoso no tóxico de alta densidad y un soplador plástico de alta durabilidad integrado en la tapa.'
WHERE id_producto = 2;