<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
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
            <form class="d-flex search-bar ms-4 me-auto">
                <input class="form-control me-2" type="search" placeholder="Buscar productos..." aria-label="Buscar">
                <a class="btn btn-warning" href="ResultadoBusqueda.php">Buscar</a>
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
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createListModal">Crear lista</a></li>
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

    <div class="modal fade" id="createListModal" tabindex="-1" aria-labelledby="createListModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style = "color:black">
            <div class="modal-header">
                <h5 class="modal-title" id="createListModalLabel">Crear Lista</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createListForm">
                    <div class="mb-3">
                        <label for="listName" class="form-label">Nombre de la lista</label>
                        <input type="text" class="form-control" id="listName" required>
                    </div>
                    <div class="mb-3">
                        <label for="listDescription" class="form-label">Descripción</label>
                        <textarea class="form-control" id="listDescription" required></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isPublic">
                        <label class="form-check-label" for="isPublic">¿Cualquiera puede ver la lista?</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="createList()">Crear Lista</button>
            </div>
        </div>
    </div>
</div>

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
    <form id="consultaVentasForm" method="post" action="">
        <div class="row mb-3">
            <div class="col">
                <label for="fechaDesde" class="form-label">Desde:</label>
                <input type="date" class="form-control" id="fechaDesde" name="fechaDesde" required>
            </div>
            <div class="col">
                <label for="fechaHasta" class="form-label">Hasta:</label>
                <input type="date" class="form-control" id="fechaHasta" name="fechaHasta" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="filtroCategoria" class="form-label">Buscar por Categoría:</label>
            <select class="form-select" id="filtroCategoria" name="filtroCategoria">
                <option value="todas">Todas</option> 
                <option value="calzado">Calzado</option>
                <option value="formal">Formal</option>
                <option value="trajesDeBaño">Trajes de Baño</option>
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
                <?php
                

                    $resultadosDetallados = [
                        ['fecha' => '2024-09-15 10:00', 'categoria' => 'Ropa Hombre', 'producto' => 'Camisa Negra (La de Juanes)', 'calificacion' => 'Me gustó', 'precio' => '$2999999.99', 'existencia' => 19], 
                        ['fecha' => '2024-09-11 08:00', 'categoria' => 'Ropa Hombre', 'producto' => 'Camisa Blanca (No la de Juanes)', 'calificacion' => 'No me gustó', 'precio' => '$0.99', 'existencia' => 17], 

                    ];

                    foreach ($resultadosDetallados as $venta) {
                        echo "<tr>
                                <td>{$venta['fecha']}</td>
                                <td>{$venta['categoria']}</td>
                                <td>{$venta['producto']}</td>
                                <td>{$venta['calificacion']}</td>
                                <td>{$venta['precio']}</td>
                                <td>{$venta['existencia']}</td>
                            </tr>";
                    }
                
                ?>
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
                <?php
                $resultadosAgrupados = [
                    ['mesAnio' => 'Sep-2024', 'categoria' => 'Ropa Hombre', 'ventas' => 2],                  

                ];

                foreach ($resultadosAgrupados as $venta) {
                    echo "<tr>
                            <td>{$venta['mesAnio']}</td>
                            <td>{$venta['categoria']}</td>
                            <td>{$venta['ventas']}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src= "ConsultaVentas.js"></script>

</body>
</html>
