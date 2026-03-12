<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $paterno = $_POST['paterno'];
    $materno = $_POST['materno'];
    $direccion = $_POST['direccion'];
    $cuota = $_POST['cuota'];
    $fecha = date('Y-m-d H:i:s'); // Fecha actual para fecha_registro

    // Consulta SQL con los nombres de columna de tu DB
    $sql = "INSERT INTO Socios (nombre_socio, ap_paterno, ap_materno, direccion, fecha_registro, cuota_actual) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $params = array($nombre, $paterno, $materno, $direccion, $fecha, $cuota);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $mensaje = "<div style='color:green; margin-bottom:20px;'>✅ Socio guardado con éxito.</div>";
    } else {
        $mensaje = "<div style='color:red; margin-bottom:20px;'>❌ Error: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Socio | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=2.1">
</head>
<body>
    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <nav>
            <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
            <a href="socios.php" class="nav-link active">👥 Socios</a>
            <a href="logout.php" class="nav-link logout">🚪 Salir</a>
        </nav>
    </div>

    <div class="main">
        <div class="card" style="max-width: 600px; margin: auto;">
            <h1>Registrar Nuevo Socio</h1>
            <p style="color: #64748b; margin-bottom: 20px;">Completa los datos del nuevo integrante.</p>
            
            <?php echo $mensaje; ?>

            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight:700; margin-bottom:5px;">Nombre</label>
                    <input type="text" name="nombre" class="form-control-login" required>
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display:block; font-weight:700; margin-bottom:5px;">Apellido Paterno</label>
                        <input type="text" name="paterno" class="form-control-login" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display:block; font-weight:700; margin-bottom:5px;">Apellido Materno</label>
                        <input type="text" name="materno" class="form-control-login">
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight:700; margin-bottom:5px;">Dirección</label>
                    <input type="text" name="direccion" class="form-control-login">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display:block; font-weight:700; margin-bottom:5px;">Cuota Inicial ($)</label>
                    <input type="number" step="0.01" name="cuota" class="form-control-login" value="0.00">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Guardar Socio</button>
                <a href="socios.php" style="display:block; text-align:center; margin-top:15px; color:#64748b; text-decoration:none;">Cancelar y Volver</a>
            </form>
        </div>
    </div>
</body>
</html>