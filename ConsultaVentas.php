<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}


$id_usuario = $_SESSION['id_usuario'];
$rol_usuario = $_SESSION['rol_usuario'];

require 'conexionBaseDeDatos.php';

if($rol_usuario === 'Vendedor')
{
$fecha_inicio = $_GET['fechaDesde'] ?? null;
$fecha_fin = $_GET['fechaHasta'] ?? null;
$categoria = $_GET['categoria'] ?? 'todas';

// Validación opcional de fechas.
if ($fecha_inicio && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio)) {
    die('Fecha de inicio no válida');
}

if ($fecha_fin && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
    die('Fecha de fin no válida');
}

// Consulta SQL dinámica con filtros.
$sql = "
    SELECT 
        t.HoraFechaTransaccion,
        c.NombreCategoria,
        p.NombreProducto,
        co.Calificación,
        t.PrecioTotalProducto,
        p.CantidadProducto AS ExistenciaActual
    FROM Transacción t
    LEFT JOIN Producto p ON t.ID_PRODUCTO = p.ID_PRODUCTO
    LEFT JOIN Categoría c ON p.ID_CATEGORIA = c.ID_CATEGORIA
    LEFT JOIN Comentario co ON t.ID_TRANSACCION = co.ID_TRANSACCION
    WHERE t.ID_USUARIO_VENDEDOR = ?
";

$sql_reporte = "
    SELECT 
        DATE_FORMAT(t.HoraFechaTransaccion, '%Y-%m') AS MesAnio,
        c.NombreCategoria,
        COUNT(t.ID_TRANSACCION) AS Ventas
    FROM Transacción t
    LEFT JOIN Producto p ON t.ID_PRODUCTO = p.ID_PRODUCTO
    LEFT JOIN Categoría c ON p.ID_CATEGORIA = c.ID_CATEGORIA
    WHERE t.ID_USUARIO_VENDEDOR = ?
    GROUP BY MesAnio, c.NombreCategoria
    ORDER BY MesAnio DESC
";

// Agregar condiciones para filtros.
$condiciones = [];
$params = [$id_usuario];
$tipos = "i";

if ($fecha_inicio) {
    $condiciones[] = "t.HoraFechaTransaccion >= ?";
    $params[] = $fecha_inicio . " 00:00:00";
    $tipos .= "s";
}

if ($fecha_fin) {
    $condiciones[] = "t.HoraFechaTransaccion <= ?";
    $params[] = $fecha_fin . " 23:59:59";
    $tipos .= "s";
}

if ($categoria && $categoria !== 'todas') {
    $condiciones[] = "c.NombreCategoria = ?";
    $params[] = $categoria;
    $tipos .= "s";
}

// Añadir condiciones a la consulta.
if ($condiciones) {
    $sql .= " AND " . implode(" AND ", $condiciones);
}

// Ordenar por fecha de transacción.
$sql .= " ORDER BY t.HoraFechaTransaccion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($tipos, ...$params);
$stmt->execute();

$resultado = $stmt->get_result();
$resultados = [];

while ($fila = $resultado->fetch_assoc()) {
    $resultados[] = $fila;
}

$stmt_reporte = $conn->prepare($sql_reporte);
$stmt_reporte->bind_param('i', $id_usuario );
$stmt_reporte->execute();
$resultado_reporte = $stmt_reporte->get_result();


$stmt->close();

}else{
    echo "Acceso no autorizado.";
    header('Location: home.php');
    exit();

}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ConsultaVentas.css" rel="stylesheet">
</head>

<body>
     <!-- Nav bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <!-- Barra de búsqueda centralizada -->
            <form action = "ResultadoBusqueda.php" method = "GET" class="d-flex search-bar ms-4 me-auto">
                <input name = "textobusqueda" class="form-control me-2" type="text" placeholder="Buscar productos..." aria-label="Buscar">
                <button type="submit" class="btn btn-warning" href="ResultadoBusqueda.php">Buscar</button>
            </form>

            <!-- Iconos de cuenta y carrito -->
            <ul class="navbar-nav">
                <!-- Cuenta y listas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/user.png" alt="Cuenta" width="20"> Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="perfil.php">Mi cuenta</a></li>
                        <li><a class="dropdown-item" href="Editarperfil.php">Editar Cuenta</a></li>
                        <li><a class="dropdown-item" href="ChatCotizacion.php">Mensajes Privados</a></li>                                                

                        <?php if ($rol_usuario === 'Vendedor'): ?>
                            <li><a class="dropdown-item" href="PublicarProducto.php">Publicar Producto</a></li>
                            <li><a class="dropdown-item" href="ConsultaVentas.php">Consultar Ventas</a></li>
                            <li><a class="dropdown-item" href="ConsultaProductos.php">Ver Mis Productos</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="HistorialDeCompras.php">Historial de Compras</a></li>
                            <li><a class="dropdown-item" href="listas.php">Mis listas</a></li>
                        <?php endif; ?>

                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#crearcategoria">Crear Categoria</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="CerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>

                <!-- Carrito de compras -->
                <li class="nav-item">
                <a class="nav-link" href="CarritoCompra.php">
                <img src="https://img.icons8.com/ios-filled/50/ffffff/shopping-cart.png" alt="Carrito" width="20"> Carrito
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    

    <!-- PARA CREAR UNA CATEGORIA SE ABRE ESTA VENTANA-->
    <div class="modal fade" id="crearcategoria" tabindex="-1" aria-labelledby="crearcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="color:black">
            <div class="modal-header">
                <h5 class="modal-title" id="crearcategoriaLabel">Crear Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCategoriaForm">
                    <div class="mb-3">
                        <label for="NameCategoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="NameCategoria" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoriadescripcion" class="form-label">Descripción de Categoría</label>
                        <textarea class="form-control" id="categoriadescripcion" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="crearCategoria()">Crear Categoría</button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h2>Consulta de Ventas</h2>
    <form action="ConsultaVentas.php" id="consultaVentasForm" method="GET" >
        <div class="row mb-3">
            <div class="col">
                <label for="fechaDesde" class="form-label">Desde:</label>
                <input type="date" class="form-control" id="fechaDesde" name="fechaDesde" >
            </div>
            <div class="col">
                <label for="fechaHasta" class="form-label">Hasta:</label>
                <input type="date" class="form-control" id="fechaHasta" name="fechaHasta" >
            </div>
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría:</label>
            <select class="form-select" id="categoria" name="categoria">
                <option value="todas">Todas</option>
                <?php
                $categorias = $conn->query("SELECT * FROM categoría");
                while ($categoria = $categorias->fetch_assoc()) {
                    $selected = ($categoriaSeleccionada === $categoria['NombreCategoria']) ? 'selected' : '';
                    echo "<option value='{$categoria['NombreCategoria']}' $selected>{$categoria['NombreCategoria']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Consultar Ventas</button>
    </form>

    <div id="resultadosDetallados" class="mt-4" style="display: block;">
        <h5>Consulta Detallada:</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Categoría</th>
                    <th>Producto</th>
                    <th>Calificación</th>
                    <th>Precio</th>
                    <th>Existencia Actual</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($resultados)): ?>
                <tr>
                    <td colspan="5" class="text-center">No se encontraron compras.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($resultados as $row) { ?>
                        <tr>
                            <td><?= $row['HoraFechaTransaccion'] ?></td>
                            <td><?= $row['NombreCategoria'] ?></td>
                            <td><?= $row['NombreProducto'] ?></td>
                            <td><?= $row['Calificación'] ?></td>
                            <td>$<?= $row['PrecioTotalProducto'] ?></td>
                            <td><?= $row['ExistenciaActual'] ?></td>
                        </tr>
                <?php } ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <div id="resultadosAgrupados" class="mt-4" style="display: block;">
        <h5>Consulta Agrupada:</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mes-Año</th>
                    <th>Categoría</th>
                    <th>Ventas</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado_reporte->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['MesAnio'] ?></td>
                        <td><?= $row['NombreCategoria'] ?></td>
                        <td><?= $row['Ventas'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="ConsultaProductos.php" style="color:yellow; font-size: 18px;">Ver mis productos</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src= "ConsultaVentas.js"></script>

</body>
</html>
