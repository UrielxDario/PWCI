<?php
session_start(); 

require 'conexionBaseDeDatos.php'; 
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
$productos_comprados = isset($_SESSION['productos_comprados']) ? $_SESSION['productos_comprados'] : [];



$id_usuario_cliente = $_SESSION['id_usuario'];
$transacciones_pendientes = $_SESSION['transacciones_pendientes'] ?? [];

if (!empty($transacciones_pendientes)) {
    $ids_transacciones = implode(",", $transacciones_pendientes);

    $query = "
        SELECT 
            t.ID_TRANSACCION,
            t.ID_PRODUCTO,
            p.NombreProducto,
            p.PrecioProducto,
            p.TipoProducto,
            p.DescripcionProducto,
            (SELECT m.Archivo FROM Multimedia m WHERE m.ID_PRODUCTO = p.ID_PRODUCTO LIMIT 1) AS ImgArchivo
        FROM Transacción t
        JOIN Producto p ON t.ID_PRODUCTO = p.ID_PRODUCTO
        WHERE t.ID_TRANSACCION IN ($ids_transacciones)";
    
    $result = $conn->query($query);
    $productos_comprados = $result->fetch_all(MYSQLI_ASSOC);

    // Limpiar las transacciones pendientes de la sesión después de consultarlas
    unset($_SESSION['transacciones_pendientes']);
}




if (isset($_POST['publicarComentarios'])) {
    // Elimina los productos y transacciones de la sesión
    unset($_SESSION['productos_comprados']);
    unset($_SESSION['transacciones_pendientes']); 

    header('Location: home.php');
    exit();
}
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

<div class='container mt-4'>
    <div class='row'>
        <div class='col-md-8'>
            <h2>Productos Comprados</h2>
            <?php
            if (empty($productos_comprados)) {
                echo "<p>No hay productos para calificar.</p>";
            } else {
                foreach ($productos_comprados as $producto) {
                    $img_binario = $producto['ImgArchivo'];
                    $img_base64 = base64_encode($img_binario);
                    $img_src = "data:image/jpeg;base64,{$img_base64}";
                    $nombre_producto = htmlspecialchars($producto['NombreProducto']);
                    $precio_producto = number_format($producto['PrecioProducto'], 2);
                    $id_transaccion = $producto['ID_TRANSACCION'];

                    echo "
                    <div class='container mb-4 producto' data-id-transaccion='{$id_transaccion}'>
                        <div class='row'>
                            <div class='col-md-3'>
                                <img src='{$img_src}' class='img-fluid' alt='Producto {$nombre_producto}'>
                            </div>
                            <div class='col-md-9'>
                                <div class='d-flex justify-content-between'>
                                    <div>
                                        <h4>{$nombre_producto}</h4>
                                        <label for='reseñaProducto{$id_transaccion}' class='form-label'>Reseña del Producto</label>
                                        <textarea class='form-control reseña-producto' id='reseñaProducto{$id_transaccion}' rows='4' placeholder='Reseña del producto' required></textarea>
                                    </div>
                                    <div class='text-end'>
                                        <p class='h5'>\$${precio_producto}</p>
                                        <button type='button' class='btn btn-success btn-gusto' data-value='Me gustó' data-group='gusto{$id_transaccion}'>Me gustó</button>
                                        <button type='button' class='btn btn-danger btn-gusto' data-value='No me gustó' data-group='gusto{$id_transaccion}'>No me gustó</button>
                                        <input type='hidden' class='gusto-seleccionado' id='gustoSeleccionado{$id_transaccion}' name='gustoSeleccionado{$id_transaccion}' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            }
            ?>
        </div>
        <div class="col-md-4">
            <div class="container total">
                <button class="btn btn-warning w-100" id="publicarComentarios">Publicar Comentarios</button>
            </div>
        </div>
    </div>
</div>






    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "CalificarProducto.js"></script>

</body>
</html>
