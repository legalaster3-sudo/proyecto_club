# 🏆 Club Pro - Sistema de Gestión de Socios y Pagos

Club Pro es una aplicación web robusta diseñada para la administración eficiente de clubes, gimnasios o asociaciones. Permite gestionar el registro de socios, el control de cobranzas mensuales y la administración de usuarios con altos estándares de seguridad.

## 🚀 Características Principales

* **Dashboard Inteligente:** Resumen en tiempo real de socios activos e ingresos totales.
* **Gestión de Socios:** CRUD completo (Crear, Leer, Actualizar, Eliminar) con validación de datos.
* **Control de Pagos:** Registro de mensualidades vinculadas a socios con historial de movimientos.
* **Seguridad Avanzada:** Sistema de login con autenticación mediante `password_hash` y protección de sesiones.
* **Interfaz Moderna:** Diseño responsive y elegante utilizando la fuente *Plus Jakarta Sans* y CSS avanzado.

## 🛠️ Tecnologías Utilizadas

* **Frontend:** HTML5, CSS3 (Custom Variables & Animations), JavaScript (Vanilla).
* **Backend:** PHP 8.x.
* **Base de Datos:** Microsoft SQL Server.
* **Conectividad:** Driver ODBC de Microsoft para PHP (`sqlsrv`).

## 📋 Requisitos Previos

1.  Servidor local (XAMPP, WAMP o Laragon).
2.  Microsoft SQL Server instalado.
3.  Drivers de SQL Server para PHP instalados y habilitados en el `php.ini`.

## ⚙️ Instalación

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/tu-usuario/club-pro.git](https://github.com/tu-usuario/club-pro.git)
    ```
2.  **Configurar la Base de Datos:**
    * Ejecuta los scripts SQL proporcionados para crear las tablas `Socios`, `Pagos` y `Usuarios`.
3.  **Configurar Conexión:**
    * Edita el archivo `config/conexion.php` con tus credenciales de SQL Server.
4.  **Acceso:**
    * Mueve la carpeta al directorio `htdocs` y accede desde `http://localhost/club-pro`.
## ✒️ Autor

* **Tu Nombre** - *Desarrollo Inicial* - [7HONNY](https://github.com/)

---
Desarrollado con ❤️ para la gestión deportiva.