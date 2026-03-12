<?php
session_start();
require_once "config/conexion.php";

// Verificamos la sesión con el nuevo nombre de la columna PK: 'id'
if (!isset($_SESSION["id"])) {
    header("Location: index.php");
    exit();
}

// --- CONSULTAS PARA EL RESUMEN ---
// 1. Total de Socios
$res_socios = sqlsrv_query($conn, "SELECT COUNT(*) as total FROM Socios");
$f_socios = sqlsrv_fetch_array($res_socios, SQLSRV_FETCH_ASSOC);
$total_socios = $f_socios['total'];

// 2. Total Recaudado (Usando tu tabla Pagos)
$res_pagos = sqlsrv_query($conn, "SELECT SUM(monto) as total FROM Pagos");
$f_pagos = sqlsrv_fetch_array($res_pagos, SQLSRV_FETCH_ASSOC);
$total_dinero = $f_pagos['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control | Club Pro</title>
    <link rel="stylesheet" href="css/estilos.css?v=11.0">
</head>
<body style="display: flex; background-color: #f8fafc; margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <div class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h2 style="color: white; margin: 0; letter-spacing: 2px;">CLUB PRO</h2>
        </div>
        
        <nav style="margin-top: 20px; flex-grow: 1;">
            <a href="panel_control.php" class="nav-link active">🏠 Inicio</a>
            <a href="socios.php" class="nav-link">👥 Socios</a>
            <a href="pagos.php" class="nav-link">💰 Pagos y Cuotas</a>
            <a href="usuarios.php" class="nav-link">🔑 Usuarios</a>
            <a href="logout.php" class="nav-link logout" style="margin-top: 50px;">🚪 Salir</a>
        </nav>
    </div>

    <div class="main" style="flex-grow: 1; padding: 40px;">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <div>
                <h1 style="margin: 0; color: #1e293b; font-size: 1.8rem;">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
                <p style="color: #64748b; margin: 5px 0 0;">Este es el resumen general de tu club hoy.</p>
            </div>
            <div style="background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <span style="color: #94a3b8; font-size: 12px; font-weight: bold; display: block;">FECHA ACTUAL</span>
                <span style="color: #1e293b; font-weight: bold;"><?php echo date('d/m/Y'); ?></span>
            </div>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
            
            <div class="card" style="border-left: 5px solid #3b82f6;">
                <span style="color: #64748b; font-size: 14px; font-weight: 600;">SOCIOS ACTIVOS</span>
                <h2 style="font-size: 2.5rem; margin: 10px 0; color: #1e293b;"><?php echo $total_socios; ?></h2>
                <a href="socios.php" style="color: #3b82f6; text-decoration: none; font-size: 13px; font-weight: bold;">Ver lista completa →</a>
            </div>

            <div class="card" style="border-left: 5px solid #10b981;">
                <span style="color: #64748b; font-size: 14px; font-weight: 600;">INGRESOS TOTALES</span>
                <h2 style="font-size: 2.5rem; margin: 10px 0; color: #1e293b;">$<?php echo number_format($total_dinero, 2); ?></h2>
                <a href="pagos.php" style="color: #10b981; text-decoration: none; font-size: 13px; font-weight: bold;">Revisar historial →</a>
            </div>

            <div class="card" style="border-left: 5px solid #f59e0b;">
                <span style="color: #64748b; font-size: 14px; font-weight: 600;">ACCESOS RÁPIDOS</span>
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <a href="socios.php" class="btn btn-primary" style="padding: 8px 12px; font-size: 12px; text-decoration: none;">+ Nuevo Socio</a>
                </div>
            </div>

        </div>

        <div style="margin-top: 40px; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
            <h3 style="margin-top: 0; color: #1e293b;">Estado del Sistema</h3>
            <p style="color: #64748b; font-size: 14px;">El sistema está conectado a la base de datos SQL Server satisfactoriamente. Todos los módulos (Socios, Pagos y Usuarios) se encuentran operativos bajo el protocolo de seguridad de hashes.</p>
        </div>
    </div>

</body>
</html>