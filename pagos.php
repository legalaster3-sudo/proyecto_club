<?php
// 1. Activar reporte de errores para ver qué falla exactamente
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "config/conexion.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

// PROCESAR REGISTRO DE PAGO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_pago'])) {
    $id_socio = $_POST['id_socio'];
    $monto = $_POST['monto'];
    $detalle_pago = "Mensualidad: " . $_POST['mes_pagado'];
    $fecha = date('Y-m-d H:i:s');

    $sql = "INSERT INTO Pagos (id_socio, monto, fecha_pago, detalle) VALUES (?, ?, ?, ?)";
    $params = array($id_socio, $monto, $fecha, $detalle_pago);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        $mensaje = "<div style='background:#dcfce7; color:#166534; padding:15px; border-radius:10px; margin-bottom:20px;'>✅ Pago registrado correctamente.</div>";
    } else {
        $mensaje = "<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:10px; margin-bottom:20px;'>❌ Error al insertar: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}

// OBTENER LISTA DE SOCIOS (Asegúrate que estos nombres coinciden con tu imagen de la tabla Socios)
$res_socios = sqlsrv_query($conn, "SELECT id_socio, nombre_socio, ap_paterno FROM Socios");
if ($res_socios === false) {
    die("Error en consulta de socios: " . print_r(sqlsrv_errors(), true));
}

// OBTENER HISTORIAL (Usamos LEFT JOIN para que no falle si la tabla pagos está vacía)
$query_historial = "SELECT P.monto, P.fecha_pago, P.detalle, S.nombre_socio, S.ap_paterno 
                    FROM Pagos P 
                    LEFT JOIN Socios S ON P.id_socio = S.id_socio 
                    ORDER BY P.id_pago DESC";
$res_pagos = sqlsrv_query($conn, $query_historial);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagos | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=5.0">
</head>
<body style="display: flex; background: #f8fafc;">

    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <nav style="flex-grow: 1;">
            <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
            <a href="socios.php" class="nav-link">👥 Socios</a>
            <a href="pagos.php" class="nav-link active">💰 Pagos</a>
            <a href="logout.php" class="nav-link logout">🚪 Salir</a>
        </nav>
    </div>

    <div class="main" style="width: 100%; padding: 40px;">
        <h1 style="margin-bottom: 20px;">Gestión de Pagos</h1>
        
        <?php echo $mensaje; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
            <div class="card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 15px;">Registrar Cobro</h3>
                <form method="POST">
                    <input type="hidden" name="registrar_pago" value="1">
                    <label style="display:block; font-size: 12px; font-weight: bold; margin-bottom: 5px;">SOCIO</label>
                    <select name="id_socio" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; margin-bottom:15px;" required>
                        <option value="">Seleccionar...</option>
                        <?php while($s = sqlsrv_fetch_array($res_socios, SQLSRV_FETCH_ASSOC)): ?>
                            <option value="<?php echo $s['id_socio']; ?>">
                                <?php echo $s['nombre_socio'] . " " . $s['ap_paterno']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label style="display:block; font-size: 12px; font-weight: bold; margin-bottom: 5px;">MES</label>
                    <input type="text" name="mes_pagado" placeholder="Enero 2026" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; margin-bottom:15px;" required>

                    <label style="display:block; font-size: 12px; font-weight: bold; margin-bottom: 5px;">MONTO ($)</label>
                    <input type="number" step="0.01" name="monto" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; margin-bottom:15px;" required>

                    <button type="submit" class="btn btn-primary" style="width:100%; cursor:pointer;">Guardar Pago</button>
                </form>
            </div>

            <div class="card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 15px;">Historial Reciente</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; font-size: 12px; color: #64748b; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 10px;">SOCIO</th>
                            <th style="padding: 10px;">MONTO</th>
                            <th style="padding: 10px;">FECHA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res_pagos): ?>
                            <?php while($p = sqlsrv_fetch_array($res_pagos, SQLSRV_FETCH_ASSOC)): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9; font-size: 14px;">
                                <td style="padding: 10px;"><strong><?php echo $p['nombre_socio']; ?></strong></td>
                                <td style="padding: 10px; color: #10b981; font-weight: bold;">$<?php echo number_format($p['monto'], 2); ?></td>
                                <td style="padding: 10px; font-size: 12px; color: #64748b;">
                                    <?php echo ($p['fecha_pago'] instanceof DateTime) ? $p['fecha_pago']->format('d/m/Y') : '---'; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>