<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Solicitudes pendientes</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="public/js/auth.js"></script>
    <script src="public/js/solicitud.js"></script>
    <script src="public/js/jquery-4.0.0.min.js"></script>




</head>

<body class="container mt-5">
    <nav>
        <div class="nav-contenedor">
            <a class="nav-item" href="index.php?page=talleres">Talleres</a>
            <a class="nav-item" href="index.php?page=admin">Gestionar Solicitudes</a>
        </div>
        <div class="nav-contenedor-sesion">
            <span class="nav-rol">Admin: <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['user'] ?? 'Administrador') ?></span>
            <button id="btnLogout" class="btn-logout btn btn-primary">Cerrar sesión</button>
        </div>
    </nav>

    <main>
        <h2>Solicitudes pendientes de aprobación</h2>

        <div class=" table-container">
            <span id="sin-solicitudes"></span>
            <table id="tabla-solicitudes" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Taller</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="solicitudes-body">
                </tbody>
            </table>
        </div>
    </main>

    <div id="mensaje"></div>


</body>

</html>