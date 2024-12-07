<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}


$id_usuario = $_SESSION['id_usuario'];
$rol_usuario = $_SESSION['rol_usuario'];

require 'conexionBaseDeDatos.php';

if($rol_usuario === 'Cliente')
{
$fecha_inicio = $_GET['fecha-inicio'] ?? null;
$fecha_fin = $_GET['fecha-fin'] ?? null;
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
        t.PrecioTotalProducto
    FROM Transacción t
    LEFT JOIN Producto p ON t.ID_PRODUCTO = p.ID_PRODUCTO
    LEFT JOIN Categoría c ON p.ID_CATEGORIA = c.ID_CATEGORIA
    LEFT JOIN Comentario co ON t.ID_TRANSACCION = co.ID_TRANSACCION
    WHERE t.ID_USUARIO_CLIENTE = ?
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
    <title>Historial de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/HistorialDeCompras.css" rel="stylesheet">
</head>

<body>
     <!-- Nav bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <!-- Barra de búsqueda -->
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


    <!-- Filtros de búsqueda -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Historial de Compras</h2>

        <form action="HistorialDeCompras.php" method="GET" class="mb-4">
            <div class="row">
                <!-- Rango de fechas -->
                <div class="col-md-4">
                    <label for="fecha-inicio" class="form-label">Fecha de inicio:</label>
                    <input type="date" class="form-control" id="fecha-inicio" name="fecha-inicio">
                </div>
                <div class="col-md-4">
                    <label for="fecha-fin" class="form-label">Fecha de fin:</label>
                    <input type="date" class="form-control" id="fecha-fin" name="fecha-fin">
                </div>
                <!-- Filtro por categoría -->
                <div class="col-md-4">
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
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Buscar Compras</button>
                </div>
            </div>
        </form>

        <!-- Tabla con resultados de compras -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    
                </thead>
                <tbody>
                    <?php if (empty($resultados)): ?>
                        <tr>
                            <td colspan="5" class="text-center">No se encontraron compras en el rango seleccionado.</td>
                        </tr>
                    <?php else: ?>
                        <?php
                            $ultimaFechaHora = null;
                            $totalCompra = 0; // Para calcular el total por cada compra.

                            foreach ($resultados as $compra): 
                                // Si la fecha y hora cambia, cerramos la tabla anterior y mostramos el total.
                                if ($ultimaFechaHora !== $compra['HoraFechaTransaccion']) {
                                    if ($ultimaFechaHora !== null) { // Evitar mostrar antes de la primera tabla
                                        echo "<tr>
                                                <td colspan='4' class='text-end fw-bold'>Total:</td>
                                                <td class='fw-bold'>$" . number_format($totalCompra, 2) . "</td>
                                            </tr>
                                            </tbody>
                                            </table>";
                                    }
                                    // Nueva tabla para la nueva compra.
                                    $ultimaFechaHora = $compra['HoraFechaTransaccion'];
                                    $totalCompra = 0; // Reiniciar el total para la nueva compra.

                                    echo "<h4>Compra realizada el: " . htmlspecialchars($ultimaFechaHora) . "</h4>";
                                    echo "<table class='table table-bordered'>
                                            <thead class='table-dark'>
                                                <tr>
                                                    <th scope='col'>Fecha y Hora de Compra</th>
                                                    <th scope='col'>Categoría</th>
                                                    <th scope='col'>Nombre del Producto</th>
                                                    <th scope='col'>Calificación</th>
                                                    <th scope='col'>Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                }

                                // Mostrar fila del producto.
                                echo "<tr>
                                        <td>" . htmlspecialchars($compra['HoraFechaTransaccion']) . "</td>
                                        <td>" . htmlspecialchars($compra['NombreCategoria'] ?? 'Sin categoría') . "</td>
                                        <td>" . htmlspecialchars($compra['NombreProducto']) . "</td>
                                        <td>" . htmlspecialchars($compra['Calificación'] ?? 'Sin calificación') . "</td>
                                        <td>$" . number_format($compra['PrecioTotalProducto'], 2) . "</td>
                                    </tr>";

                                // Acumular el total de la compra actual.
                                $totalCompra += $compra['PrecioTotalProducto'];
                            endforeach;

                            // Mostrar el total de la última tabla.
                            if ($ultimaFechaHora !== null) {
                                echo "<tr>
                                        <td colspan='4' class='text-end fw-bold'>Total:</td>
                                        <td class='fw-bold'>$" . number_format($totalCompra, 2) . "</td>
                                    </tr>
                                    </tbody>
                                    </table>";
                            }
                        ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "HistorialDeCompras.js"></script>

</body>
</html>
