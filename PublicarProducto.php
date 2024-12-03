<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
require 'conexionBaseDeDatos.php';

// Bloque para actualizar categorías
if (isset($_GET['actualizarCategorias'])) {
    $query = "SELECT ID_CATEGORIA, NombreCategoria FROM Categoría";
    $result = $conn->query($query);

    $categorias = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
    }
    echo json_encode(['success' => true, 'categorias' => $categorias]);
    exit();
}

// Bloque para crear una nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombreCategoria'], $_POST['descripcionCategoria'])) {
    $nombreCategoria = mysqli_real_escape_string($conn, $_POST['nombreCategoria']);
    $descripcionCategoria = mysqli_real_escape_string($conn, $_POST['descripcionCategoria']);

    $queryCrearCategoria = "INSERT INTO Categoría (NombreCategoria, DescripcionCategoria) VALUES ('$nombreCategoria', '$descripcionCategoria')";
    $resultadoCrearCategoria = $conn->query($queryCrearCategoria);

    if ($resultadoCrearCategoria) {
        echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al crear la categoría']);
    }
    exit();
}

// Bloque principal: mostrar categorías y manejar la inserción del producto
$query = "SELECT ID_CATEGORIA, NombreCategoria FROM Categoría";
$result = $conn->query($query);

$categorias = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

//Insert para Producto y su multimedia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombreProducto'], $_POST['descripcionProducto'], $_POST['categoriaProducto'])) {
    $user_id = $_SESSION['id_usuario'];

    $nombreProducto = mysqli_real_escape_string($conn, $_POST['nombreProducto']);
    $descripcionProducto = mysqli_real_escape_string($conn, $_POST['descripcionProducto']);
    $idCategoria = (int)$_POST['categoriaProducto']; // ID de categoría obtenido del select

    // Verificar si está marcado el check de cotización
    $esCotizacion = isset($_POST['cotizacionCheck']) ? true : false;

    $tipoProducto = $esCotizacion ? "Para Cotizar" : "Normal";
    $precioProducto = $esCotizacion ? "NULL" : (float)$_POST['precioProducto'];
    $cantidadProducto = $esCotizacion ? "NULL" : (int)$_POST['cantidadProducto'];
    $promedioCalificacion = "NULL";
    $autorizacionAdmin = "Pendiente";

    // Insertar en la tabla Producto
    $queryProducto = "INSERT INTO Producto (NombreProducto, DescripcionProducto, TipoProducto, PrecioProducto, CantidadProducto, PromedioCalificacion, AutorizacionAdmin, ID_CATEGORIA, ID_USUARIO) 
                      VALUES ('$nombreProducto', '$descripcionProducto', '$tipoProducto', $precioProducto, $cantidadProducto, $promedioCalificacion, '$autorizacionAdmin', $idCategoria, $user_id)";
    
    $resultadoProducto = mysqli_query($conn, $queryProducto);

   
    if ($resultadoProducto) {
        $idProducto = mysqli_insert_id($conn);

        // Manejo de imágenes (requiere al menos 3 imágenes)
        if (isset($_FILES['productImages'])) {
            $imagenes = $_FILES['productImages'];
    
            // Comprobar que el array contiene más de un archivo
            if (is_array($imagenes['tmp_name'])) {
                foreach ($imagenes['tmp_name'] as $index => $imagenTmp) {
                    // Convertir la imagen a formato BLOB
                    $imagenBlob = file_get_contents($imagenTmp);
                    $imagenBlob = mysqli_real_escape_string($conn, $imagenBlob);
                    
                    // Insertar cada imagen en la tabla Multimedia
                    $queryImagen = "INSERT INTO Multimedia (Archivo, ID_PRODUCTO) VALUES ('$imagenBlob', $idProducto)";
                    $resultadoImagen = mysqli_query($conn, $queryImagen);
    
                    if (!$resultadoImagen) {
                        echo json_encode(['success' => false, 'error' => 'Error al insertar imagen']);
                        exit();
                    }
                }
            } else {
                $imagenTmp = $imagenes['tmp_name'];
                $imagenBlob = file_get_contents($imagenTmp);
                $imagenBlob = mysqli_real_escape_string($conn, $imagenBlob);
    
                $queryImagen = "INSERT INTO Multimedia (Archivo, ID_PRODUCTO) VALUES ('$imagenBlob', $idProducto)";
                $resultadoImagen = mysqli_query($conn, $queryImagen);
    
                if (!$resultadoImagen) {
                    echo json_encode(['success' => false, 'error' => 'Error al insertar imagen']);
                    exit();
                }
            }
        }
        

        // Manejo del video (requiere un video)
        if (isset($_FILES['productVideo'])) {
            $videoTmp = $_FILES['productVideo']['tmp_name'];
            
            $videoBlob = file_get_contents($videoTmp);
            $videoBlob = mysqli_real_escape_string($conn, $videoBlob);

            $queryVideo = "INSERT INTO Multimedia (Archivo, ID_PRODUCTO) VALUES ('$videoBlob', $idProducto)";
            $resultadoVideo = mysqli_query($conn, $queryVideo);

            if (!$resultadoVideo) {
                echo json_encode(['success' => false, 'error' => 'Error al insertar video']);
                exit();
            }
        }

        echo "<script> alert('Producto cargado con Éxito. Espere a que un administrador autorize su producto para publicarlo');
            window.location.href = 'PublicarProducto.php';  // Redirige a la página de publicación
          </script>";
    } else {
        echo json_encode(['success' => false, 'error' => 'Error en la inserción del producto']);
    }
}


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/PublicarProducto.css" rel="stylesheet">
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


 <!-- Formulario de publicación de producto -->
 <div class="container mt-5">
        <div class="card bg-dark text-white">
            <div class="card-header text-warning">
                <h3>Publicar Producto</h3>
            </div>
            <div class="card-body">
                <form id="formularioproducto" method="POST" enctype="multipart/form-data">
                    <!-- Nombre del producto -->
                    <div class="mb-3">
                        <label for="nombreProducto" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Ingrese el nombre del producto" >
                    </div>

                    <!-- Descripción del producto -->
                    <div class="mb-3">
                        <label for="descripcionProducto" class="form-label">Descripción del Producto</label>
                        <textarea class="form-control" id="descripcionProducto"  name="descripcionProducto" rows="4" placeholder="Descripción del producto" ></textarea>
                    </div>

                    <!-- Precio del producto -->
                    <div class="mb-3">
                        <label for="precioProducto" class="form-label">Precio del Producto</label>
                        <input type="number" class="form-control" id="precioProducto" name="precioProducto" placeholder="Ingrese el precio del producto" step="0.01" >
                    </div>

                     <!-- Categoría del producto -->
                    <div class="mb-3">
                        <label for="categoriaProducto" class="form-label">Categoría</label>
                        <select class="form-control" id="categoriaProducto" name="categoriaProducto" >
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['ID_CATEGORIA'] ?>"><?= htmlspecialchars($categoria['NombreCategoria']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cantidad del producto -->
                    <div class="mb-3">
                        <label for="cantidadProducto" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="cantidadProducto" name="cantidadProducto" min="1" value="1" >
                    </div>

                    <!-- Adjuntar imágenes y video -->
                    <div class="mb-3">
                        <label for="productMedia" class="form-label">Adjuntar Imágenes (3 obligatoriamente) y 1 Video</label>
                        <input type="file" class="form-control" id="productImages" name="productImages[]" accept="img/*" multiple >
                        <input type="file" class="form-control mt-2" id="productVideo" name="productVideo" accept="img/*" >
                    </div>

                    <div id="previewImages" class="d-flex flex-wrap gap-2 mb-3"></div>
                    <div id="previewVideo" class="mb-3"></div>

                    <!-- Producto para cotización -->
                    <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="cotizacionCheck" name="cotizacionCheck" onclick="cambioprecio()">
                    <label class="form-check-label" for="cotizacionCheck">¿El producto es para cotizar?</label>
                    </div>

                    <!-- Botón para publicar -->
                    <button type="submit" class="btn btn-warning w-100">Publicar Producto</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="PublicarProducto.js"></script>
   
</body>
</html>
