<?php
include 'conexionBaseDeDatos.php';
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol_usuario'] !== 'Vendedor') {
    header("Location: login.php");
    exit();
}

$id_vendedor = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que los datos requeridos estén presentes
    if (isset($_POST['id_cliente'], $_POST['id_chat'], $_POST['nombreProducto'], $_POST['descripcionProducto'], $_POST['precioProducto'], $_FILES['imagenProducto'])) {
        $id_cliente = $_POST['id_cliente'];
        $id_chat = $_POST['id_chat'];
        $nombreProducto = trim($_POST['nombreProducto']);
        $descripcionProducto = trim($_POST['descripcionProducto']);
        $precioProducto = floatval($_POST['precioProducto']);
        $imagenProducto = $_FILES['imagenProducto'];
        $autorizacionAdmin = "No";
        $tipoProducto = "Cotizado";
        $promedioCalificacion = NULL;
        $idCategoria = 1;
        $cantidadProducto = 1;

        // Validar y procesar la imagen
        if ($imagenProducto['error'] === UPLOAD_ERR_OK) {
            $imagenTmpPath = $imagenProducto['tmp_name'];
            $imagenContenido = file_get_contents($imagenTmpPath);
            $imagenTipo = $imagenProducto['type'];
        } else {
            die("Error al cargar la imagen del producto.");
        }

        // Insertar el producto en la base de datos
        $query = "INSERT INTO producto (NombreProducto, DescripcionProducto, TipoProducto, PrecioProducto, CantidadProducto, PromedioCalificacion, AutorizacionAdmin, ID_CATEGORIA, ID_USUARIO) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdisssi", $nombreProducto, $descripcionProducto, $tipoProducto, $precioProducto, $cantidadProducto, $promedioCalificacion, $autorizacionAdmin, $idCategoria, $id_vendedor);
        if ($stmt->execute()) {
            $id_producto = $conn->insert_id;

            // Insertar la imagen en la tabla multimedia
            $query_multimedia = "INSERT INTO multimedia ( Archivo,ID_PRODUCTO) VALUES (?, ?)";
            $stmt_multimedia = $conn->prepare($query_multimedia);
            $stmt_multimedia->bind_param('si', $imagenContenido, $id_producto);
            if ($stmt_multimedia->execute()) {
                // Redirigir al chat original con mensaje de éxito
                echo "Producto creado correctamente.";
                header("Location: chat.php?id_chat=" . $id_chat . "&mensaje=producto_creado");
                exit();
            } else {
                die("Error al guardar la imagen del producto.");
            }
        } else {
            die("Error al guardar el producto en la base de datos.");
        }
    } else {
        die("Datos incompletos para crear el producto.");
    }
} else {
    header("Location: chat.php");
    exit();
}
?>
