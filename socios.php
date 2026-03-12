<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

if (isset($_GET['eliminar'])) {
    $id_del = $_GET['eliminar'];
    $sql = "DELETE FROM Socios WHERE id_socio = ?";
    if (sqlsrv_query($conn, $sql, array($id_del))) {
        $mensaje = "<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:1rem; margin-bottom:20px;'>🗑️ Socio eliminado.</div>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_socio'])) {
    $id = $_POST['id_socio'];
    $nom = $_POST['nombre_socio'];
    $pat = $_POST['ap_paterno'];
    $mat = $_POST['ap_materno'];
    $dir = $_POST['direccion'];
    $cuo = $_POST['cuota_actual'];

    $sql = "UPDATE Socios SET nombre_socio=?, ap_paterno=?, ap_materno=?, direccion=?, cuota_actual=? WHERE id_socio=?";
    $params = array($nom, $pat, $mat, $dir, $cuo, $id);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        $mensaje = "<div style='background:#dcfce7; color:#166534; padding:15px; border-radius:1rem; margin-bottom:20px;'>✏️ Datos actualizados correctamente.</div>";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_socio'])) {
    $sql = "INSERT INTO Socios (nombre_socio, ap_paterno, ap_materno, direccion, fecha_registro, cuota_actual) VALUES (?, ?, ?, ?, GETDATE(), ?)";
    sqlsrv_query($conn, $sql, array($_POST['nombre_socio'], $_POST['ap_paterno'], $_POST['ap_materno'], $_POST['direccion'], $_POST['cuota_actual']));
    $mensaje = "<div style='background:#dcfce7; color:#166534; padding:15px; border-radius:1rem; margin-bottom:20px;'>✅ Socio registrado.</div>";
}

$res_socios = sqlsrv_query($conn, "SELECT * FROM Socios ORDER BY id_socio DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Socios | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=30.0">
</head>
<body style="display: flex;">

    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <nav>
            <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
            <a href="socios.php" class="nav-link active">👥 Socios</a>
            <a href="pagos.php" class="nav-link">💰 Pagos</a>
            <a href="usuarios.php" class="nav-link">🔑 Usuarios</a>
            <a href="logout.php" class="nav-link logout">🚪 Salir</a>
        </nav>
    </div>

    <div class="main">
        <h1>Gestión de Socios</h1>
        <?php echo $mensaje; ?>

        <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;">
            <div class="card">
                <h3>Nuevo Socio</h3>
                <form method="POST">
                    <input type="hidden" name="registrar_socio">
                    <input type="text" name="nombre_socio" placeholder="Nombre" class="form-control-login" required style="margin-bottom:10px;">
                    <input type="text" name="ap_paterno" placeholder="Ap. Paterno" class="form-control-login" required style="margin-bottom:10px;">
                    <input type="text" name="ap_materno" placeholder="Ap. Materno" class="form-control-login" style="margin-bottom:10px;">
                    <input type="text" name="direccion" placeholder="Dirección" class="form-control-login" style="margin-bottom:10px;">
                    <input type="number" step="0.01" name="cuota_actual" placeholder="Cuota $" class="form-control-login" required style="margin-bottom:15px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Guardar</button>
                </form>
            </div>

            <div class="card">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; color:var(--text-muted); border-bottom:2px solid #f1f5f9;">
                            <th style="padding:10px;">SOCIO</th>
                            <th>CUOTA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = sqlsrv_fetch_array($res_socios, SQLSRV_FETCH_ASSOC)): ?>
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:15px;">
                                <strong><?php echo $s['nombre_socio']." ".$s['ap_paterno']; ?></strong><br>
                                <small style="color:gray;"><?php echo $s['direccion']; ?></small>
                            </td>
                            <td>$<?php echo number_format($s['cuota_actual'], 2); ?></td>
                            <td>
                                <button onclick='abrirEditar(<?php echo json_encode($s); ?>)' style="color:var(--primary); background:none; border:none; cursor:pointer; font-weight:bold;">Editar</button> | 
                                <a href="socios.php?eliminar=<?php echo $s['id_socio']; ?>" onclick="return confirm('¿Eliminar socio?')" style="color:var(--danger); text-decoration:none; font-weight:bold;">Borrar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalEditar" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:2000;">
        <div class="card" style="width:400px; margin-bottom:0;">
            <h3>Editar Socio</h3>
            <form method="POST">
                <input type="hidden" name="editar_socio">
                <input type="hidden" name="id_socio" id="edit_id">
                <input type="text" name="nombre_socio" id="edit_nom" class="form-control-login" required style="margin-bottom:10px;">
                <input type="text" name="ap_paterno" id="edit_pat" class="form-control-login" required style="margin-bottom:10px;">
                <input type="text" name="ap_materno" id="edit_mat" class="form-control-login" style="margin-bottom:10px;">
                <input type="text" name="direccion" id="edit_dir" class="form-control-login" style="margin-bottom:10px;">
                <input type="number" step="0.01" name="cuota_actual" id="edit_cuo" class="form-control-login" required style="margin-bottom:15px;">
                <button type="submit" class="btn btn-primary" style="width:100%;">Actualizar Datos</button>
                <button type="button" onclick="document.getElementById('modalEditar').style.display='none'" style="width:100%; background:none; border:none; margin-top:10px; cursor:pointer; color:var(--text-muted);">Cancelar</button>
            </form>
        </div>
    </div>

    <script>
    function abrirEditar(socio) {
        document.getElementById('edit_id').value = socio.id_socio;
        document.getElementById('edit_nom').value = socio.nombre_socio;
        document.getElementById('edit_pat').value = socio.ap_paterno;
        document.getElementById('edit_mat').value = socio.ap_materno;
        document.getElementById('edit_dir').value = socio.direccion;
        document.getElementById('edit_cuo').value = socio.cuota_actual;
        document.getElementById('modalEditar').style.display = 'flex';
    }
    </script>
</body>
</html>