<?php
// Configuración de las credenciales de la base de datos (Entorno Local)
$host = 'localhost';
$db_name = 'exocube_db';
$username = 'root'; // Usuario por defecto de XAMPP / WampServer
$password = '';     // Contraseña por defecto (vacía en entornos locales)
$charset = 'utf8mb4'; // Juego de caracteres óptimo para soporte de acentos y caracteres especiales

// Construcción de la cadena DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

// Opciones de configuración de PDO para mayor seguridad y control de errores
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de errores de SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna los resultados como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad contra inyecciones SQL
];

try {
    // Creación de la instancia de conexión PDO
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Opcional para pruebas locales (puedes borrar o comentar esta línea una vez verificado)
    // echo "Conexión exitosa a la base de datos de exo_cube.";
    
} catch (\PDOException $e) {
    // En entornos de producción, es importante capturar el error sin revelar las credenciales al usuario.
    // Registramos un mensaje controlado en su lugar.
    die("Error crítico de conexión: No se pudo establecer contacto con el servidor de datos.");
    
    // Si estás en desarrollo local y necesitas ver el error detallado, puedes descomentar la siguiente línea:
    // die("Error detallado: " . $e->getMessage());
}
?>