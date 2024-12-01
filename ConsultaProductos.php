<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
$id_usuario = $_SESSION['id_usuario'];

// Conexión a la base de datos
require 'conexionBaseDeDatos.php';

// Consulta para obtener categorías
$sql_categorias = "SELECT ID_CATEGORIA, NombreCategoria FROM categoría";
$resultado_categorias = $conn->query($sql_categorias);
$categorias = $resultado_categorias->fetch_all(MYSQLI_ASSOC);

// Consulta para obtener productos y sus imágenes
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : 'todas';
$sql = "SELECT 
            p.NombreProducto AS nombre_producto, 
            p.PrecioProducto, 
            p.CantidadProducto, 
            m.Archivo AS imagen_url,
            p.AutorizacionAdmin as autorizado

        FROM 
            producto p
        LEFT JOIN 
            multimedia m 
        ON 
            p.ID_PRODUCTO = m.ID_PRODUCTO 
        WHERE
            p.ID_USUARIO = ?" ;         

if ($categoriaSeleccionada !== 'todas') {
    $sql .= " AND p.ID_CATEGORIA = ? ";
} else {
    $sql .= " GROUP BY p.ID_PRODUCTO";
}

$stmt = $conn->prepare($sql);
if ($categoriaSeleccionada !== 'todas') {
    $stmt->bind_param('ii', $id_usuario, $categoriaSeleccionada); 
} else {
    $stmt->bind_param('i', $id_usuario);
}

$stmt->execute();
$resultado = $stmt->get_result();
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Productos</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ConsultaProductos.css" rel="stylesheet">
    
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


    <!-- Filtro por Categoría -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Consulta de Productos por Categoría</h2>

        <form action="ConsultaProductos.php" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="categoria" class="form-label">Mostrar por categoría:</label>
                    <select class="form-select" id="categoria" name="categoria">
                        <option value="todas" <?= $categoriaSeleccionada === 'todas' ? 'selected' : '' ?>>Todas las categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria['ID_CATEGORIA'] ?>" <?= $categoriaSeleccionada == $categoria['ID_CATEGORIA'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['NombreCategoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>

        <!-- Productos -->
        <div class="row">
            <?php if (count($productos) === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        ¡No has agregado ningún producto aún! Empieza publicando tus productos en la ventana Publicar Producto.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card producto">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen_url']); ?>" class="img-fluid imagen-producto" alt="Producto <?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                            <div class="card-body cuerpo-producto">
                                <h5 class="card-title titulo-producto"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h5>
                                <p class="card-text precio-producto precio-pesos"> $<?php echo number_format($producto['PrecioProducto'], 2); ?>MXN</p>
                                <p class="card-text precio-producto precio-dolares" data-precio="<?php echo $producto['PrecioProducto']; ?>"> USD</p>
                                
                                <p class="card-text cantidad-producto">Cantidad Disponible: <?php echo htmlspecialchars($producto['CantidadProducto']); ?></p>
                                
                                <!-- Estado de autorización -->
                                <p class="card-text estado-autorizacion 
                                    <?php 
                                        if ($producto['autorizado'] == 'Si') {
                                            echo 'autorizado'; // Clase para autorizado
                                        } elseif ($producto['autorizado'] == 'Pendiente') {
                                            echo 'pendiente'; // Clase para pendiente
                                        } else {
                                            echo 'denegado'; // Clase para denegado
                                        }
                                    ?>">
                                    <?php 
                                        if ($producto['autorizado'] == 'Si') {
                                            echo 'Autorizado';
                                        } elseif ($producto['autorizado'] == 'Pendiente') {
                                            echo 'Pendiente de autorización';
                                        } else {
                                            echo 'No autorizado';
                                        }
                                    ?>
                                </p>
                            
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

   
    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "ConsultaProductos.js"></script>
    <script src= "ApiConversionMoneda.js" ></script>

</body>
</html>
