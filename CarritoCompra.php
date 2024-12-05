<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];


$id_usuario = $_SESSION['id_usuario'];

require 'conexionBaseDeDatos.php';

$query = "
    SELECT 
        p.ID_PRODUCTO,
        p.NombreProducto AS Titulo,
        p.PrecioProducto AS Precio,
        p.CantidadProducto,
        pec.CantidadAgregada,
        (pec.CantidadAgregada * p.PrecioProducto) AS TotalProducto,
        (
            SELECT m.Archivo 
            FROM multimedia m 
            WHERE m.ID_PRODUCTO = p.ID_PRODUCTO 
            LIMIT 1
        ) AS ImgArchivo,
        c.ID_CARRITO 
    FROM ProductoEnCarrito pec
    INNER JOIN Carrito c ON pec.ID_CARRITO = c.ID_CARRITO
    INNER JOIN Producto p ON pec.ID_PRODUCTO = p.ID_PRODUCTO
    WHERE c.ID_USUARIO = ?
";



$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/CarritoCompra.css" rel="stylesheet">
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
        <div class="col-md-8">
            <h2>Productos en el Carrito</h2>
            <?php if (empty($productos)): ?>
                <p>No hay productos en tu carrito.</p>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="container mb-4 producto">
                        <div class="row">
                            <!-- Imagen del Producto -->
                            <div class="col-md-3">
                                <img src="data:image/jpeg;base64,<?= base64_encode($producto['ImgArchivo']) ?>" 
                                    class="img-fluid" 
                                    alt="Producto <?= htmlspecialchars($producto['Titulo']) ?>">
                            </div>

                            <!-- Detalles del Producto -->
                            <div class="col-md-9">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= htmlspecialchars($producto['Titulo']) ?></h4>
                                        <input type="number" 
                                         id="cantidad"
                                            class="form-control w-50 cantidad-producto" 
                                            value="<?= $producto['CantidadAgregada'] ?>" 
                                            min="1" 
                                            max="<?php echo htmlspecialchars($producto['CantidadProducto']); ?>" 
                                            data-precio="<?= $producto['Precio'] ?>" 
                                            data-id="<?= $producto['ID_PRODUCTO'] ?>">
                                    </div>
                                    <div class="text-end">
                                        <p class="h5">$<?= number_format($producto['Precio'], 2) ?></p>
                                        <button class="btn btn-danger eliminar-producto" 
                                            data-id="<?= $producto['ID_PRODUCTO'] ?>" 
                                            data-carrito="<?= $producto['ID_CARRITO'] ?>" 
                                            data-usuario="<?= $_SESSION['id_usuario'] ?>">Eliminar
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Sección del Total -->
        <div class="col-md-4">
            <h2>Total</h2>
            <div class="container total">
                <p class="h5">Total: <span id="total">$0.00</span></p>
                <a class="btn btn-warning w-100" href="#" data-bs-toggle="modal" data-bs-target="#procederpago">Proceder al Pago</a>
            </div>
        </div>
    </div>
</div>




    <!-- PARA ELEGIR EL METODO DE PAGO SE ABRE ESTA VENTANA-->
<div class="modal fade" id="procederpago" tabindex="-1" aria-labelledby="procederpagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style = "color:black">
            <div class="modal-header">
                <h5 class="modal-title" id="procederpagoLabel">Elige Tu Método de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="procederpagoForm">
                    <div class="mb-3">
                        <label for="metodoPago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodoPago" onchange="mostrarInputNumero()" required>
                            <option value="" disabled selected>Selecciona un método</option>
                            <option value="paypal">Paypal</option>
                            <option value="efectivo">Efectivo en Oxxo</option>
                            <option value="tarjeta">Tarjeta de Crédito</option>
                        </select>
                    </div>
                    <div class="mb-3" id="inputNumeroContainer" style="display: none;">
                        <label for="numeroPago" class="form-label">Ingrese el número</label>
                        <input type="text" class="form-control" id="numeroPago">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a class="btn btn-primary" href="CalificarProducto.php">Confirmar Compra</a>

            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src= "CarritoCompra.js"></script>
</body>
</html>
