<?php
session_start();
require_once "config/conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['usuario'] ?? '';
    $pass_input = $_POST['password'] ?? '';

    // PROBANDO CON NOMBRES ESTÁNDAR: id, usuario, password
    // Si sigue fallando, cambia 'usuario' por 'nombre' o 'nick'
    $sql = "SELECT id, usuario, password FROM Usuarios WHERE usuario = ?";
    
    $params = array($user_input);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Si falla, mostramos los nombres reales de las columnas para corregirlo de una vez
        echo "<div style='background:#1a1a1a; color:#f87171; padding:20px; border-radius:10px; font-family:monospace;'>";
        echo "<h3>❌ Error de Columnas</h3>";
        echo "SQL dice que los nombres están mal. Intenta cambiarlos en el código.<br><br>";
        die(print_r(sqlsrv_errors(), true));
        echo "</div>";
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Usamos los índices que pusimos en el SELECT (id, usuario, password)
        if ($pass_input == $row['password']) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];
            
            header("Location: panel_control.php");
            exit();
        } else {
            $error = "La contraseña es incorrecta.";
        }
    } else {
        $error = "El usuario '$user_input' no existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Club Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --bg: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; padding: 2.5rem; border-radius: 24px; box-shadow: 0 20px 25px rgba(0,0,0,0.2); width: 100%; max-width: 380px; text-align: center; }
        h1 { color: #0f172a; font-weight: 800; margin-bottom: 0.5rem; }
        .form-group { text-align: left; margin-bottom: 1.2rem; }
        label { display: block; font-weight: 700; font-size: 0.75rem; color: #475569; margin-bottom: 8px; text-transform: uppercase; }
        input { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1rem; box-sizing: border-box; outline: none; }
        input:focus { border-color: var(--primary); }
        .btn { width: 100%; background: var(--primary); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; cursor: pointer; font-size: 1rem; }
        .alert { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>CLUB PRO</h1>
        <p style="color: #64748b; margin-bottom: 2rem;">Inicia sesión para continuar</p>
        <?php if($error) echo "<div class='alert'>$error</div>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" placeholder="Ej: admin" required autofocus>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn">Entrar al Sistema</button>
        </form>
    </div>
</body>
</html>