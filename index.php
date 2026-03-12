<?php
session_start();
require_once "config/conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['usuario'];
    $pass_input = $_POST['password'];

    // Usamos los nombres de columna reales: 'usuario' y 'password'
    $sql = "SELECT id, usuario, password FROM Usuarios WHERE usuario = ?";
    $params = array($user_input);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (password_verify($pass_input, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['usuario'] = $row['usuario'];
            header("Location: panel_control.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=15.0">
</head>
<body class="login-body">

    <div class="login-card">
        <h2 style="color: var(--secondary); margin-bottom: 0.5rem;">CLUB PRO</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Inicia sesión para continuar</p>

        <?php if($error): ?>
            <div style="background: var(--danger); color: white; padding: 1rem; border-radius: 1rem; font-size: 0.9rem; margin-bottom: 1.5rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div style="text-align: left; margin-bottom: 1.5rem;">
                <label style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-left: 0.5rem;">USUARIO</label>
                <input type="text" name="usuario" class="form-control-login" placeholder="Ej: admin" required autofocus>
            </div>

            <div style="text-align: left; margin-bottom: 2rem;">
                <label style="font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-left: 0.5rem;">CONTRASEÑA</label>
                <input type="password" name="password" class="form-control-login" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Entrar al Sistema
            </button>
        </form>
    </div>

</body>
</html>