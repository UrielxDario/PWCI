<?php
require 'conexionBaseDeDatos.php';

$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

$query = "SELECT 
    p.ID_PRODUCTO,
    p.NombreProducto,
    p.PrecioProducto,
    p.DescripcionProducto,
    m.Archivo AS Imagen,
    u.Username  
    FROM Producto p
    INNER JOIN Multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
    INNER JOIN Usuario u ON p.ID_USUARIO = u.ID_USUARIO  
    WHERE p.AutorizacionAdmin = 'Pendiente'
    GROUP BY p.ID_PRODUCTO;";
$resultado = $conn->query($query);

$productosPendientes = []; 
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $productosPendientes[] = $row; 
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorizar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/AutorizarProductos.css" rel="stylesheet">
    

</head>
<body>

   <!-- Nav bar -->
   <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" >The Dark Wardrobe</a>
            
           

            <!-- Iconos de cuenta y carrito -->
            <ul class="navbar-nav">
                <!-- Cuenta y listas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/user.png" alt="Cuenta" width="20"> Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="MiPerfilAdmin.php">Mi cuenta</a></li>
                        <li><a class="dropdown-item" href="AutorizarProductos.php">Autorizar Productos</a></li>

                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="CerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>

                
            </ul>
        </div>
    </nav>




    <div class="container mt-4">
    <div class="row">
        <!-- Lista de productos por autorizar -->
        <div class="col-md-8 offset-md-2">
            <h2>Productos Faltantes Por Autorizar</h2>

            <?php if ($mensaje === 'exito'): ?>
                    <div class="alert alert-success" role="alert">
                        ¡El producto fue autorizado o denegado con éxito!
                    </div>
            <?php endif; ?>



            <?php if (!empty($productosPendientes)) : ?>
                <?php foreach ($productosPendientes as $producto) : ?>
                    <div class="container mb-4 producto">
                        <div class="row">
                            <div class="col-md-3">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['Imagen']); ?>" class="card-img-top imagen-producto" alt="Producto <?php echo htmlspecialchars($producto['NombreProducto']); ?>">

                            </div>
                            <div class="col-md-9">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo htmlspecialchars($producto['NombreProducto']); ?></h4>
                                        <p>Vendedor: <?php echo htmlspecialchars($producto['Username']); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <p class="h5">$<?php echo number_format($producto['PrecioProducto'], 2); ?></p>
                                        <!-- Formulario para autorizar producto -->
                                        <form action="Autorizar_DenegarProductos.php" method="post" style="display:inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $producto['ID_PRODUCTO']; ?>">
                                            <button type="submit" name="action" value="autorizar" class="btn btn-success">Autorizar Producto</button>
                                        </form>
                                        <!-- Formulario para denegar producto -->
                                        <form action="Autorizar_DenegarProductos.php" method="post" style="display:inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $producto['ID_PRODUCTO']; ?>">
                                            <button type="submit" name="action" value="denegar" class="btn btn-danger ms-2">Denegar Producto</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No hay productos pendientes de autorización.</p>
            <?php endif; ?>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
