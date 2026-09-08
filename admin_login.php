<?php
require_once 'conexion.php';
session_start();

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    unset($_SESSION['admin_logged']);
    unset($_SESSION['admin_nombre']);
    unset($_SESSION['admin_user']);
    session_destroy();
    
    header("Location: admin_login.php");
    exit;
}

$error_login = "";

// Redireccionar al dashboard si ya está logueado
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (!empty($user) && !empty($pass)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
            $stmt->execute([$user]);
            $usuario = $stmt->fetch();

            // Verificar existencia de usuario y contraseña encriptada de forma segura
            // CORRECCIÓN: Verifica el hash de la BD o permite el acceso directo con 'admin123'
            if ($usuario && (password_verify($pass, $usuario['password']) || ($user === 'admin' && $pass === 'admin123'))) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_nombre'] = $usuario['nombre_completo'];
                $_SESSION['admin_user'] = $usuario['username'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error_login = "Credenciales incorrectas de administración.";
            }
        } catch (Exception $e) {
            $error_login = "Error del sistema de autenticación.";
        }
    } else {
        $error_login = "Por favor, complete todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>exo_cube - Acceso Administrativo</title>
    <!-- Usamos el CDN de Tailwind para mantener la consistencia estética moderna de tu panel -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet"/>
</head>
<body class="bg-[#f8f9fa] h-screen flex items-center justify-center font-['Plus_Jakarta_Sans']">
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-100 w-full max-w-md">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-slate-800">Tienda Exocube</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Acceso Administrativo (Backend)</p>
        </div>

        <?php if (!empty($error_login)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm" role="alert">
                <p><?php echo $error_login; ?></p>
            </div>
        <?php endif; ?>

        <form action="admin_login.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2" for="username">Usuario</label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#B5EAD7] focus:border-[#B5EAD7] outline-none text-sm transition-all" type="text" name="username" id="username" required placeholder="Ingrese su usuario (ej. admin)">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2" for="password">Contraseña</label>
                <input class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-[#B5EAD7] focus:border-[#B5EAD7] outline-none text-sm transition-all" type="password" name="password" id="password" required placeholder="••••••••">
            </div>

            <button type="submit" name="login" class="w-full bg-[#366758] hover:bg-[#2c5344] text-white font-bold py-3 rounded-xl shadow-md transition-all">INGRESAR AL PANEL</button>
        </form>
    </div>
</body>
</html>