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
    <title>Chat Cotizacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ChatCotizacion.css" rel="stylesheet">
</head>

<body>
     <!-- Nav bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <!-- Barra de búsqueda xd -->
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

    <!-- Contenido del Chat -->
    <header class="container my-4">
        <h2 id="titulo-chat">Mensajes Privados - Cotizaciones de Productos</h2>
        <p>Conversación entre Cliente y Vendedor</p>
    </header>

    <div class="container">
    <div class="row">
        <!-- Lista de Vendedores / Cotizaciones -->
        <div class="col-md-3">
            <h4>Vendedores / Cotizaciones</h4>
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="#" class="vendedor-enlace">Walter White- Cotización de Camisa Negra</a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="vendedor-enlace">Peter Parker - Cotización de Mochila Personalizada</a>
                </li>
                <li class="list-group-item">
                    <a href="#" class="vendedor-enlace">Miles Morales- Cotización de Sneakers Pintados</a>
                </li>
            </ul>
        </div>

        <!-- Mensajes -->
        <div class="col-md-9">
            <div class="contenedor-mensajes">
                <!-- Mensaje del Cliente -->
                <div class="mensaje cliente">
                    <img src="img/Dui.jpg" alt="Cliente">
                    <div class="contenido-mensaje">
                        <p>Hola Mr. White, estoy interesado en el precio de la camisa.</p>
                        <span class="marca-tiempo">23/09/2024 10:15</span>
                    </div>
                </div>

                <!-- Mensaje del Vendedor -->
                <div class="mensaje vendedor">
                    <img src="img/Walter.jpg" alt="Vendedor">
                    <div class="contenido-mensaje">
                        <p>Claro Jesse, la camisa cuesta $420. ¿Le interesa algún otro producto? Guiño Guiño</p>
                        <span class="marca-tiempo">23/09/2024 10:20</span>
                    </div>
                </div>
            </div>

            <!-- Enviar Nuevo Mensaje -->
            <div class="enviar-mensaje">
                <textarea class="form-control" rows="3" placeholder="Escribe tu mensaje aquí..."></textarea>
                <button class="btn btn-enviar">Enviar</button>
            </div>
        </div>
    </div>
</div>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="ChatCotizacion.js"></script>

</body>
</html>
