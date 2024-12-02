<?php
session_start();

// Redirigir al login si no hay una sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];

include 'conexionBaseDeDatos.php'; 

$categoriaSeleccionada = $_GET['categoria'] ?? 'ninguno';
$ordenSeleccionado = $_GET['orden'] ?? 'Default';
$palabra = $_GET['textobusqueda'] ?? '';

$sql = "(
    SELECT 
        producto.ID_PRODUCTO, 
        producto.NombreProducto AS Titulo, 
        producto.PrecioProducto AS Valor, 
        producto.DescripcionProducto AS Detalle, 
        producto.TipoProducto AS TipoDeProducto,
        categoría.NombreCategoria AS Categoria, 
        multimedia.Archivo AS ImgArchivo, 
        NULL AS ImgPerfil, 
        NULL AS Username, 
        NULL AS NombreCompleto, 
        NULL AS Rol
    FROM producto
    INNER JOIN categoría ON producto.ID_CATEGORIA = categoría.ID_CATEGORIA
    LEFT JOIN multimedia ON producto.ID_PRODUCTO = multimedia.ID_PRODUCTO
    WHERE producto.AutorizacionAdmin = 'Si' " . 
    (!empty($palabra) ? "AND producto.NombreProducto LIKE '%$palabra%'" : "") . "
    " . ($categoriaSeleccionada !== 'ninguno' ? "AND categoría.NombreCategoria = '$categoriaSeleccionada'" : "") . "
    GROUP BY producto.ID_PRODUCTO
)";

// Si no se seleccionó una categoría, agregar los usuarios a la consulta
if ($categoriaSeleccionada === 'ninguno') {
    $sql .= " UNION
    (
        SELECT 
            NULL AS ID_PRODUCTO,
            NULL AS Titulo,
            NULL AS Valor,
            NULL AS Detalle,
            NULL AS TipoDeProducto,
            NULL AS Categoria,
            NULL AS ImgArchivo,
            usuario.ImgPerfil AS ImgPerfil,
            usuario.Username AS Username,
            CONCAT(usuario.Nombre, ' ', usuario.ApellidoPaterno, ' ', usuario.ApellidoMaterno) AS NombreCompleto,
            usuario.Rol AS Rol
        FROM usuario
        WHERE 
            usuario.Username LIKE '%$palabra%' OR 
            usuario.Nombre LIKE '%$palabra%' OR 
            usuario.ApellidoPaterno LIKE '%$palabra%' OR 
            usuario.ApellidoMaterno LIKE '%$palabra%'
    )";
}

if ($ordenSeleccionado === 'precioAsc') {
    $sql .= " ORDER BY Valor ASC";
} elseif ($ordenSeleccionado === 'precioDesc') {
    $sql .= " ORDER BY Valor DESC";
}

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

$productos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ResultadoBusqueda.css" rel="stylesheet">
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

    <!-- Filtros de búsqueda -->
    <div class="container mt-4">
        <form method="GET" id="formFiltros">
            <div class="row">
                <div class="col-md-6">
                    <label for="filtroBusqueda" class="form-label">Ordenar por:</label>
                    <select class="form-select" id="filtroBusqueda" name="orden">
                        <option value="Default">Predeterminado</option>
                        <option value="precioAsc" <?= ($ordenSeleccionado === 'precioAsc') ? 'selected' : '' ?>>Precio: Del más bajo al más alto</option>
                        <option value="precioDesc" <?= ($ordenSeleccionado === 'precioDesc') ? 'selected' : '' ?>>Precio: Del más alto al más bajo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filtroCategoria" class="form-label">Buscar por Categoría:</label>
                    <select class="form-select" id="filtroCategoria" name="categoria">
                        <option value="ninguno">Ninguno</option>
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
        </form>
    </div>

  
    
    <div class="container mt-4">
    <?php if (empty($productos)): ?>
        <p>No se encontraron productos que coincidan con la búsqueda.</p>
    <?php else: ?>
        <?php foreach ($productos as $producto): ?>
            <div class="row mb-4 producto">
                <div class="col-md-3">
                    <?php if (!empty($producto['ImgPerfil'])): ?>
                        <img src="img/<?php echo htmlspecialchars($producto['ImgPerfil']) ?: 'user.jpg'; ?>" class="card-img-top imagen-usuario" alt="Usuario <?php echo htmlspecialchars($producto['Username']); ?>">
                    <?php else: ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['ImgArchivo']); ?>" class="card-img-top imagen-producto" alt="Producto <?php echo htmlspecialchars($producto['Titulo']); ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <div class="d-flex justify-content-between">
                        <div>
                            <?php if (!empty($producto['Username'])): ?>
                                <h4><?= htmlspecialchars($producto['Username']) ?></h4>
                                <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($producto['NombreCompleto']) ?></p>
                                <p><strong>Rol:</strong> <?= htmlspecialchars($producto['Rol']) ?></p>
                            <?php else: ?>
                                <h4>
                                    <a class= "linkver" href="VerProducto.php?id_producto=<?= htmlspecialchars($producto['ID_PRODUCTO']) ?>">
                                        <?= htmlspecialchars($producto['Titulo']) ?>
                                    </a>
                                </h4>
                                <p><strong>Categoría:</strong> <?= htmlspecialchars($producto['Categoria']) ?></p>
                                <p><strong>Descripción:</strong> <?= htmlspecialchars($producto['Detalle']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (empty($producto['Username'])): ?>
                            <div class="text-end">
                                <?php if ($producto['TipoDeProducto'] === 'Para Cotizar'): ?>
                                    <p class="h5">Para Cotización</p>
                                <?php else: ?>
                                    <p class="h5 precio-pesos">$<?= number_format($producto['Valor'], 2) ?> MXN</p>
                                    <p class="h5 precio-dolares" data-precio="<?= $producto['Valor'] ?>">USD</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>





    <script>
    document.getElementById('filtroBusqueda').addEventListener('change', function() {
    document.getElementById('formFiltros').submit();
    });

    document.getElementById('filtroCategoria').addEventListener('change', function() {
        document.getElementById('formFiltros').submit();
    });    

    </script>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "ResultadoBusqueda.js"></script> 
    <script src= "ApiConversionMoneda.js"></script> 

</body>
</html>
