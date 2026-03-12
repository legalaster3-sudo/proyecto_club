<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

// 1. PROCESAR EL REGISTRO DE PAGO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_pago'])) {
    $id_socio = $_POST['id_socio'];
    $monto = $_POST['monto'];
    $detalle = $_POST['mes_pago']; 

    $sql = "INSERT INTO Pagos (id_socio, monto, fecha_pago, detalle) VALUES (?, ?, GETDATE(), ?)";
    $params = array($id_socio, $monto, $detalle);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $mensaje = "<div class='alert alert-success'>💰 Pago registrado con éxito.</div>";
    } else {
        $mensaje = "<div class='alert alert-error'>❌ Error al registrar el pago.</div>";
    }
}

// 2. OBTENER LISTA DE SOCIOS
$res_socios = sqlsrv_query($conn, "SELECT id_socio, nombre_socio, ap_paterno FROM Socios ORDER BY nombre_socio ASC");

// 3. OBTENER ÚLTIMOS PAGOS
$sql_historial = "SELECT p.id_pago, p.monto, p.fecha_pago, p.detalle, s.nombre_socio, s.ap_paterno 
                  FROM Pagos p 
                  INNER JOIN Socios s ON p.id_socio = s.id_socio 
                  ORDER BY p.id_pago DESC";
$res_historial = sqlsrv_query($conn, $sql_historial);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body style="display: flex;">

    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <nav>
            <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
            <a href="socios.php" class="nav-link">👥 Socios</a>
            <a href="pagos.php" class="nav-link active">💰 Pagos</a>
            <a href="usuarios.php" class="nav-link">🔑 Usuarios</a>
            <a href="logout.php" class="nav-link logout">🚪 Salir</a>
        </nav>
    </div>

    <div class="main">
        <h1>Gestión de Pagos</h1>
        <?php echo $mensaje; ?>

        <div style="display: flex; gap: 2rem; align-items: flex-start;">
            
            <div class="card" style="flex: 1; max-width: 350px;">
                <h3>Nuevo Pago</h3>
                <form method="POST">
                    <input type="hidden" name="registrar_pago">
                    <div class="form-group">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">Socio</label>
                        <select name="id_socio" class="form-control-login" required>
                            <option value="">Seleccione...</option>
                            <?php if($res_socios): ?>
                                <?php while($s = sqlsrv_fetch_array($res_socios, SQLSRV_FETCH_ASSOC)): ?>
                                    <option value="<?php echo $s['id_socio']; ?>">
                                        <?php echo htmlspecialchars($s['nombre_socio'] . " " . $s['ap_paterno']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">Detalle / Mes</label>
                        <input type="text" name="mes_pago" placeholder="Ej: Marzo 2026" class="form-control-login" required>
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600; display:block; margin-bottom:8px;">Monto ($)</label>
                        <input type="number" step="0.01" name="monto" placeholder="0.00" class="form-control-login" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Registrar Cobro</button>
                </form>
            </div>

            <div class="card" style="flex: 2;">
                <h3>Historial Reciente</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Socio</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_historial): ?>
                            <?php while($p = sqlsrv_fetch_array($res_historial, SQLSRV_FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['nombre_socio'] . " " . $p['ap_paterno']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['detalle']); ?></td>
                                <td style="color:var(--success); font-weight:bold;">$<?php echo number_format($p['monto'], 2); ?></td>
                                <td><?php echo ($p['fecha_pago'] instanceof DateTime) ? $p['fecha_pago']->format('d/m/Y') : $p['fecha_pago']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No hay registros.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>