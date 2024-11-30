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
    <title>Detalles del Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/VerProducto.css" rel="stylesheet">
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

    <!-- Contenedor principal del producto -->
    <div class="container mt-5 p-4">
        <div class="row">
            <!-- Columna izquierda: carrusel de imágenes y videos -->
            <div class="col-md-6">
                <!-- Carrusel -->
                <div id="productoCarrusel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="img/producto2.jpg" class="d-block w-100" alt="Imagen 1">
                        </div>
                        <div class="carousel-item">
                            <img src="img/producto2.jpg" class="d-block w-100" alt="Imagen 2">
                        </div>
                        <div class="carousel-item">
                            <img src="img/producto2.jpg" class="d-block w-100" alt="Imagen 3">
                        </div>
                        <div class="carousel-item">
                            <video class="d-block w-100" controls>
                                <source src="video_producto.mp4" type="video/mp4">
                                Tu navegador no soporta el video.
                            </video>
                        </div>
                    </div>
                    <!-- Flechas de navegación -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#productoCarrusel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productoCarrusel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>

                <!-- Miniaturas de imágenes/videos -->
                <div class="mt-3 d-flex justify-content-center">
                    <img src="img/producto2.jpg" class="img-thumbnail me-2" alt="Miniatura 1" onclick="cambiarImagen(0)">
                    <img src="img/producto2.jpg" class="img-thumbnail me-2" alt="Miniatura 2"  onclick="cambiarImagen(1)">
                    <img src="img/producto2.jpg" class="img-thumbnail me-2" alt="Miniatura 3"  onclick="cambiarImagen(2)">
                    <img src="img/producto2.jpg" class="img-thumbnail" alt="Miniatura Video"  onclick="cambiarImagen(3)">
                    
                </div>
            </div>

            <!-- Columna derecha: detalles del producto -->
            <div class="col-md-6 text-light">
                <!-- Nombre del producto -->
                <h1>Camisa Negra (La de Juanes)</h1>
                <!-- Precio del producto -->
                <h3 class="text-warning">$29999999.99</h3>
                <!-- Stock disponible -->
                <p>Cantidad disponible en stock: <span class="text-success">20 unidades</span></p>
                <!-- Descripción del producto -->
                <p>Descripción del producto: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>

                <!-- Comentarios del producto -->
                <h5>Comentarios</h5>
                <div class="container border p-2 mb-2">
                    <p><strong>Cliente 1</strong> - Me gustó</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="container border p-2 mb-2" >
                    <p><strong>Cliente 2</strong> - No me gustó</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="container border p-2" >
                    <p><strong>Cliente 3</strong> - Me gustó</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>

                <!-- Contador de cantidad -->
                <div class="mt-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" id="cantidad" class="form-control w-25" value="1" min="1" max="20">
                </div>

                <!-- Botones de acción -->
                <div class="mt-4">
                    <button class="btn btn-warning w-100 mb-2" >Agregar al carrito</button>
                    <button class="btn btn-secondary w-100">Agregar a una lista</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script src= "VerProducto.js"></script>
</body>

</html>
