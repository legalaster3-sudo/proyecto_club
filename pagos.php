<?php
session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";


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


$res_socios = sqlsrv_query($conn, "SELECT id_socio, nombre_socio, ap_paterno FROM Socios ORDER BY nombre_socio ASC");


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
            
            <div class="card" style="flex: 2; background: white; padding: 25px; border-radius: 20px;">
    <h3 style="margin-bottom: 1.5rem;">Historial Reciente</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; color: #64748b; border-bottom: 2px solid #f1f5f9;">
                <th style="padding: 12px;">Socio</th>
                <th>Mes</th>
                <th>Monto</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if($res_historial): ?>
                <?php while($p = sqlsrv_fetch_array($res_historial, SQLSRV_FETCH_ASSOC)): ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px;">
                        <strong><?php echo htmlspecialchars($p['nombre_socio'] . " " . $p['ap_paterno']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($p['detalle'] ?? 'Sin dato'); ?></td>
                    
                    <td style="color: #10b981; font-weight: bold;">
                        $<?php echo number_format($p['monto'], 2); ?>
                    </td>
                    <td style="color: #64748b;">
                        <?php echo ($p['fecha_pago'] instanceof DateTime) ? $p['fecha_pago']->format('d/m/Y') : $p['fecha_pago']; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding: 20px;">No hay pagos registrados.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>