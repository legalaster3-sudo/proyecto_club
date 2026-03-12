<?php
session_start();
require_once "config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // trim() elimina espacios accidentales al inicio o final
    $user = trim($_POST['usuario']);
    $pass = trim($_POST['password']);

    $sql = "SELECT id, usuario, nombre, password FROM Usuarios WHERE usuario = ?";
    $stmt = sqlsrv_query($conn, $sql, array($user));

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Comparación directa
        if ($row['password'] == $pass) {
            $_SESSION["id_usuario"] = $row['id'];
            $_SESSION["usuario"] = $row['usuario'];
            $_SESSION["nombre"] = $row['nombre'];
            header("Location: panel_control.php");
            exit();
        }
    }
    
    // Si llega aquí, falló
    echo "<script>alert('Credenciales inválidas'); window.location.href='index.php';</script>";
}
?>