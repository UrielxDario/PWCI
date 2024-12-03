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
    <title>Calificar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/CalificarProducto.css" rel="stylesheet">
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

    <div class="container mt-4">
        <div class="row">
            <!-- Lista de productos -->
            <div class="col-md-8">
                <h2>Productos Comprados</h2>
                

                <!-- Producto 2 -->
                <div class="container mb-4 producto">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="img/producto2.jpg" class="img-fluid" alt="Producto 2">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>Camisa Negra (La de Juanes)</h4>
                                    <label for="reseñaProducto" class="form-label">Reseña del Producto</label>
                                    <textarea class="form-control" id="reseñaProducto" rows="4" placeholder="Reseña del producto" required></textarea>

                                </div>
                                <div class="text-end">
                                    <p class="h5">$29999999.99</p>
                                    <button class="btn btn-success" onclick="eliminarProducto(2)">Me gustó</button>

                                    <button class="btn btn-danger" onclick="eliminarProducto(2)">No me gustó</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Producto 1 -->
                <div class="container mb-4 producto">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="img/producto1.jpg" class="img-fluid" alt="Producto 2">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>Camisa Blanca (No La de Juanes)</h4>
                                    <label for="reseñaProducto" class="form-label">Reseña del Producto</label>
                                    <textarea class="form-control" id="reseñaProducto" rows="4" placeholder="Reseña del producto" required></textarea>
                                </div>
                                <div class="text-end">
                                    <p class="h5">$0.99</p>
                                   <button class="btn btn-success" onclick="eliminarProducto(2)">Me gustó</button>

                                    <button class="btn btn-danger" onclick="eliminarProducto(2)">No me gustó</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hacer Comentarios-->
            <div class="col-md-4">
                
                <div class="container total">
     
                    <a class="btn btn-warning w-100" href="#" data-bs-toggle="modal" data-bs-target="#procederpago">Publicar Comentarios</a>

         
                </div>
            </div>
        </div>
    </div>


    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "CalificarProducto.js"></script>

</body>
</html>
