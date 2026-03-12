<?php
session_start();
require_once "config/conexion.php";

// Usamos 'id' porque así se llama en tu tabla Usuarios
if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

// --- CREAR USUARIO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_usuario'])) {
    $user = $_POST['usuario_nuevo'];
    $pass = password_hash($_POST['password_nuevo'], PASSWORD_DEFAULT); 
    $nom  = $_POST['nombre_real'];

    // Usamos los nombres de columna de tu imagen: usuario, password, nombre
    $sql = "INSERT INTO Usuarios (usuario, password, nombre) VALUES (?, ?, ?)";
    $stmt = sqlsrv_query($conn, $sql, array($user, $pass, $nom));
    
    if ($stmt) {
        $mensaje = "<div style='background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;'>✅ Usuario guardado.</div>";
    } else {
        $mensaje = "<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px;'>❌ Error SQL.</div>";
    }
}

// --- ELIMINAR USUARIO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_eliminar'])) {
    $id_admin = $_SESSION["id"];
    $id_a_borrar = $_POST['id_a_borrar'];
    $pass_confirm = $_POST['pass_admin'];

    // Buscamos por 'id'
    $query_admin = "SELECT password FROM Usuarios WHERE id = ?";
    $stmt_admin = sqlsrv_query($conn, $query_admin, array($id_admin));
    $admin = sqlsrv_fetch_array($stmt_admin, SQLSRV_FETCH_ASSOC);

    if ($admin && password_verify($pass_confirm, $admin['password'])) {
        $sql_del = "DELETE FROM Usuarios WHERE id = ?";
        sqlsrv_query($conn, $sql_del, array($id_a_borrar));
        $mensaje = "<div style='background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;'>✅ Eliminado correctamente.</div>";
    } else {
        $mensaje = "<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px;'>❌ Clave de admin incorrecta.</div>";
    }
}

// --- LISTADO (Usando 'id' y 'usuario') ---
$res_users = sqlsrv_query($conn, "SELECT id, usuario, nombre FROM Usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=10.0">
</head>
<body style="display: flex; background: #f8fafc;">

    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <nav style="flex-grow: 1;">
            <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
            <a href="socios.php" class="nav-link">👥 Socios</a>
            <a href="pagos.php" class="nav-link">💰 Pagos</a>
            <a href="usuarios.php" class="nav-link active">🔑 Usuarios</a>
            <a href="logout.php" class="nav-link logout">🚪 Salir</a>
        </nav>
    </div>

    <div class="main" style="width: 100%; padding: 40px;">
        <h1>Gestión de Usuarios</h1>
        <?php echo $mensaje; ?>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px; margin-top: 20px;">
            <div class="card" style="background:white; padding:20px; border-radius:15px;">
                <h3>Nuevo Acceso</h3>
                <form method="POST">
                    <input type="hidden" name="crear_usuario" value="1">
                    <label style="display:block; margin:10px 0 5px; font-size:12px;">NOMBRE REAL</label>
                    <input type="text" name="nombre_real" class="form-control-login" required>
                    <label style="display:block; margin:10px 0 5px; font-size:12px;">USUARIO (LOGIN)</label>
                    <input type="text" name="usuario_nuevo" class="form-control-login" required>
                    <label style="display:block; margin:10px 0 5px; font-size:12px;">CONTRASEÑA</label>
                    <input type="password" name="password_nuevo" class="form-control-login" required>
                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:15px;">Crear</button>
                </form>
            </div>

            <div class="card" style="background:white; padding:20px; border-radius:15px;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom:2px solid #f1f5f9; color:#64748b;">
                            <th style="padding:10px;">ID</th>
                            <th>USUARIO</th>
                            <th>NOMBRE</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($u = sqlsrv_fetch_array($res_users, SQLSRV_FETCH_ASSOC)): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:15px;">#<?php echo $u['id']; ?></td>
                            <td><strong><?php echo $u['usuario']; ?></strong></td>
                            <td><?php echo $u['nombre']; ?></td>
                            <td>
                                <?php if($u['id'] != $_SESSION['id']): ?>
                                    <button onclick="borrar(<?php echo $u['id']; ?>)" style="color:red; background:none; border:none; cursor:pointer;">Eliminar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalPass" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
        <div style="background:white; padding:30px; border-radius:15px; width:400px;">
            <h3>Confirmar con tu clave</h3>
            <form method="POST">
                <input type="hidden" name="confirmar_eliminar" value="1">
                <input type="hidden" name="id_a_borrar" id="id_del">
                <input type="password" name="pass_admin" class="form-control-login" placeholder="Tu contraseña" required>
                <button type="submit" class="btn" style="background:red; color:white; width:100%; margin-top:10px;">Eliminar Ahora</button>
                <button type="button" onclick="document.getElementById('modalPass').style.display='none'" style="width:100%; margin-top:5px; background:none; border:none; cursor:pointer;">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
        function borrar(id) {
            document.getElementById('id_del').value = id;
            document.getElementById('modalPass').style.display = 'flex';
        }
    </script>
</body>
</html>