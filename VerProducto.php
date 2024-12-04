<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
 
include 'conexionBaseDeDatos.php';  

if (isset($_GET['id_producto']) && is_numeric($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);
} else {
    header('Location: home.php');
    exit();
}

$query = "SELECT p.NombreProducto, p.PrecioProducto, p.DescripcionProducto, p.CantidadProducto, p.TipoProducto, p.ID_USUARIO as ID_Vendedor
          FROM producto p 
          WHERE p.ID_PRODUCTO = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $producto = $result->fetch_assoc();
} else {
    header('Location: home.php');
    exit();
}

$query_imagenes = "SELECT Archivo FROM multimedia WHERE ID_PRODUCTO = ? ORDER BY ID_MULTIMEDIA ASC";
$stmt_imagenes = $conn->prepare($query_imagenes);
$stmt_imagenes->bind_param("i", $id_producto);
$stmt_imagenes->execute();
$result_imagenes = $stmt_imagenes->get_result();

$imagenes = [];
while ($row = $result_imagenes->fetch_assoc()) {
    $imagenes[] = $row['Archivo'];
}

if (empty($imagenes)) {
    $imagenes[] = "img/user.jpg"; 
}
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

    <!-- Contenedor principal del producto -->
    <div id="producto" data-id="<?php echo $id_producto; ?>">
    </div>
    
    <div class="container mt-5 p-4">
        <div class="row">
            <div class="col-md-6">
                <!-- Carrusel -->
                <div id="productoCarrusel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($imagenes as $index => $archivo): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php if ($index === count($imagenes) - 1): ?>
                                    <video class="d-block w-100" controls>
                                        <source src="data:video/mp4;base64,<?php echo base64_encode($archivo); ?>" type="video/mp4">
                                        Tu navegador no soporta el video.
                                    </video>
                                <?php else: ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($archivo); ?>" class="d-block w-100" alt="Imagen <?php echo $index + 1; ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productoCarrusel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productoCarrusel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
                            
                
                        
            <!-- Columna derecha: detalles del producto -->
            <div class="col-md-6 text-light">
                <h1><?php echo htmlspecialchars($producto['NombreProducto']); ?></h1>
                <h3 class="text-warning">$<?php echo number_format($producto['PrecioProducto'], 2); ?></h3>
                <p>Cantidad disponible en stock: <span class="text-success"><?php echo $producto['CantidadProducto']; ?> unidades</span></p>
                <p>Descripción del producto: <?php echo htmlspecialchars($producto['DescripcionProducto']); ?></p>
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
                    <input type="number" id="cantidad" class="form-control w-25" value="1" min="1" max="<?php echo $producto['CantidadProducto']; ?>"
                </div>                    
                <!-- Botones de acción -->
                <div class="mt-4">
                    <?php if ($producto['TipoProducto'] === 'Para Cotizar'): ?>
                        <!-- Botón de mensaje al vendedor -->
                        <a href="inicioChat.php?id_vendedor=<?php echo $producto['ID_Vendedor']; ?>" class="btn btn-secondary w-100">Enviar mensaje al vendedor</a> <br>
                    <?php else: ?>
                        <!-- Botón de agregar al carrito -->
                        <button class="btn btn-warning w-100 mb-2">Agregar al carrito</button>
                    <?php endif; ?>
                    <br>
                    <button class="btn btn-secondary w-100" id="abrirModalListasBtn">Agregar a una lista</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para seleccionar una lista -->
    <div class="modal fade" id="modalListas" tabindex="-1" aria-labelledby="modalListasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalListasLabel">Selecciona una lista</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <select id="listaSeleccionada" class="form-select">
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="confirmarAgregarListaBtn">Agregar a la lista</button>
        </div>
        </div>
    </div>
    </div>


    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script src= "VerProducto.js"></script>
</body>

</html>
