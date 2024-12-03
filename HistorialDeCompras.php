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
                        <option value="todas">Todas las categorías</option>
                        <option value="ropa">Ropa</option>
                        <option value="accesorios">Accesorios</option>
                        <option value="calzado">Calzado</option>
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
            <table class="table table-bordered ">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Fecha y Hora de Compra</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Nombre del Producto</th>
                        <th scope="col">Calificación</th>
                        <th scope="col">Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Ejemplo de compra 1 -->
                    <tr>
                        <td>2024-09-01 14:35</td>
                        <td>Ropa</td>
                        <td>Camisa Negra (La de Juanes)</td>
                        <td>Me gustó</td>
                        <td>$299999.99</td>
                    </tr>
                    <!-- Ejemplo de compra 2 -->
                    <tr>
                        <td>2023-12-07 18:10</td>
                        <td>Calzado</td>
                        <td>Air Jordan 1</td>
                        <td>Me gustó</td>
                        <td>$5999.99</td>
                    </tr>
                    <!-- Ejemplo de compra 3 -->
                    <tr>
                        <td>2024-09-15 09:45</td>
                        <td>Accesorios</td>
                        <td>Lentes de Sol</td>
                        <td>No me gustó</td>
                        <td>$49.99</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "HistorialDeCompras.js"></script>

</body>
</html>
