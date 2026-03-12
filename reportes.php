<?php
session_start();
require_once "config/conexion.php";
if (!isset($_SESSION["id_usuario"])) { header("Location: index.php"); exit(); }

$sql = "SELECT mes_correspondiente, SUM(monto) as total FROM Pagos GROUP BY mes_correspondiente";
$res = sqlsrv_query($conn, $sql);
$meses = []; $montos = [];
while($r = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
    $meses[] = $r['mes_correspondiente']; $montos[] = $r['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>CLUB PRO</h2>
        <a href="panel_control.php" class="nav-link">🏠 Inicio</a>
        <a href="socios.php" class="nav-link">👥 Socios</a>
        <a href="pagos.php" class="nav-link">💰 Cuotas</a>
        <a href="reportes.php" class="nav-link active">📊 Reportes</a>
    </div>
    <div class="main">
        <h1>Análisis de Ingresos</h1>
        <div class="card">
            <canvas id="chartIngresos" height="100"></canvas>
        </div>
    </div>
    <script>
        new Chart(document.getElementById('chartIngresos'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($meses); ?>,
                datasets: [{ label: 'Ingresos ($)', data: <?php echo json_encode($montos); ?>, backgroundColor: '#2563eb', borderRadius: 8 }]
            }
        });
    </script>
</body>
</html>