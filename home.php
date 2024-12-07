<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];

require 'conexionBaseDeDatos.php';
// Consulta para productos mejor calificados
$sqlMejorCalificados = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, m.Archivo 
                        FROM Producto p
                        JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
                        WHERE p.AutorizacionAdmin = 'Si'
                        GROUP BY p.ID_PRODUCTO
                        ORDER BY p.PromedioCalificacion DESC
                        LIMIT 3;";
$resultMejorCalificados = $conn->query($sqlMejorCalificados);

// Consulta para productos más vendidos
$sqlMasVendidos = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, m.Archivo, SUM(t.CantidadComprada) AS TotalVentas
                    FROM Producto p
                    JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
                    JOIN Transacción t ON p.ID_PRODUCTO = t.ID_PRODUCTO
                    WHERE p.AutorizacionAdmin = 'Si'
                    GROUP BY p.ID_PRODUCTO
                    ORDER BY TotalVentas DESC
                    LIMIT 3;";
$resultMasVendidos = $conn->query($sqlMasVendidos);

// Consulta para obtener las 3 categorías con mayor cantidad de productos
$sqlCategoriasPopulares = "SELECT c.NombreCategoria, c.ID_CATEGORIA, COUNT(p.ID_PRODUCTO) AS TotalProductos
                           FROM Categoría c
                           JOIN Producto p ON c.ID_CATEGORIA = p.ID_CATEGORIA
                           WHERE p.AutorizacionAdmin = 'Si'
                           GROUP BY c.ID_CATEGORIA
                           ORDER BY TotalProductos DESC
                           LIMIT 3;";
$resultCategoriasPopulares = $conn->query($sqlCategoriasPopulares);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Dark Wardrobe - Home</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/styles.css" rel="stylesheet">

</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <form action = "ResultadoBusqueda.php" method = "GET" class="d-flex search-bar ms-4 me-auto">
                <input name = "textobusqueda" class="form-control me-2" type="text" placeholder="Buscar productos..." aria-label="Buscar">
                <button type="submit" class="btn btn-warning" href="ResultadoBusqueda.php">Buscar</button>
            </form>

            <!-- Iconos de cuenta y carrito -->
            <ul class="navbar-nav">
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


    <!-- Sección principal de bienvenida -->
    <section class="hero-section">
      
        <video autoplay loop muted playsinline>
            <source src="img/CapaIntermediaTiendaRopa.mp4" type="video/mp4">
        </video>
           
    </section>

    <!-- Productos mejor calificados -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Productos Mejor Calificados</h2>
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
                $active = true;
                while ($producto = $resultMejorCalificados->fetch_assoc()) {
                    $imgData = base64_encode($producto['Archivo']);
                    $activeClass = $active ? 'active' : '';
                    $active = false;
                    echo "
                    <div class='carousel-item $activeClass'>
                        <div class='d-flex justify-content-center'>
                            <img src='data:image/jpeg;base64,$imgData' class='d-block w-50' alt='{$producto['NombreProducto']}'>
                        </div>
                        <div class='carousel-caption d-none d-md-block text-warning fw-bold'>
                            <h5>{$producto['NombreProducto']}</h5>
                            <p>{$producto['DescripcionProducto']}</p>
                        </div>
                    </div>";
                }
            ?>
        </div>

            <!-- Controles del carrusel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>


    <!-- Productos mas vendidos -->    
    <div class="container my-5">
        <h2 class="text-center mb-4">Productos Más Vendidos</h2>
        <div class="row">
        <?php
            while ($producto = $resultMasVendidos->fetch_assoc()) {
                $imgData = base64_encode($producto['Archivo']);
                echo "
                <div class='col-md-4'>
                    <div class='card'>
                        <img src='data:image/jpeg;base64,$imgData' class='card-img-top' alt='{$producto['NombreProducto']}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$producto['NombreProducto']}</h5>
                            <p class='card-text'>{$producto['DescripcionProducto']}</p>
                            <a class='btn btn-warning' href='VerProducto.php?id_producto=" . htmlspecialchars($producto['ID_PRODUCTO']) . "'>Comprar ahora</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>

    <!-- Productos por categorías populares -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Categorías Populares</h2>
        <?php while ($categoria = $resultCategoriasPopulares->fetch_assoc()): ?>
            <div class="mb-4">
                <h3 class="text-center"><?php echo htmlspecialchars($categoria['NombreCategoria']); ?></h3>
                <div class="row">
                    <?php
                    $idCategoria = $categoria['ID_CATEGORIA'];
                    $sqlProductosCategoria = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, m.Archivo 
                                              FROM Producto p
                                              JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
                                              WHERE p.ID_CATEGORIA = $idCategoria AND p.AutorizacionAdmin = 'Si'
                                              GROUP BY p.ID_PRODUCTO
                                              LIMIT 3;";
                    $resultProductosCategoria = $conn->query($sqlProductosCategoria);
                    while ($producto = $resultProductosCategoria->fetch_assoc()):
                        $imgData = base64_encode($producto['Archivo']);
                    ?>
                        <div class="col-md-4">
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?php echo $imgData; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['NombreProducto']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($producto['NombreProducto']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($producto['DescripcionProducto']); ?></p>
                                    <a class="btn btn-warning" href="VerProducto.php?id_producto=<?php echo htmlspecialchars($producto['ID_PRODUCTO']); ?>">Comprar ahora</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>        

    <br><br>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 The Dark Wardrobe - Todos los derechos reservados - Creado por Uriel Arguello y Luis Carrizales</p>
    </footer>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>




<script src= "home.js"></script>

</body>
</html>
