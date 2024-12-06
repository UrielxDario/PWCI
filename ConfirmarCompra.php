<?php
session_start();
require 'conexionBaseDeDatos.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$id_usuario_cliente = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); 
    $productos_carrito = $data['productos'] ?? [];

    if (empty($productos_carrito)) {
        echo json_encode(['status' => 'error', 'message' => 'No hay productos para procesar.']);
        exit();
    }

    $conn->begin_transaction(); 

    try {
        foreach ($productos_carrito as $producto) {
            $id_producto = $producto['ID_PRODUCTO'];
            $cantidad_comprada = $producto['cantidad'];
            $precio_producto = $producto['PrecioProducto'];
            $precio_total_producto = $precio_producto * $cantidad_comprada; 

            $query_producto = "
                SELECT p.NombreProducto, 
                       (SELECT m.Archivo FROM Multimedia m WHERE m.ID_PRODUCTO = p.ID_PRODUCTO LIMIT 1) AS ImgArchivo
                FROM Producto p
                WHERE p.ID_PRODUCTO = ?";
            $stmt_producto = $conn->prepare($query_producto);
            $stmt_producto->bind_param("i", $id_producto);
            $stmt_producto->execute();
            $result_producto = $stmt_producto->get_result();
            $producto_info = $result_producto->fetch_assoc();

            if (!$producto_info) {
                echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado.']);
                exit();
            }

            $nombre_producto = $producto['NombreProducto']; 
            $img_producto = $producto['ImgArchivo']; 

            // Obtener el ID del vendedor
            $query_vendedor = "SELECT ID_USUARIO FROM Producto WHERE ID_PRODUCTO = ?";
            $stmt_vendedor = $conn->prepare($query_vendedor);
            $stmt_vendedor->bind_param("i", $id_producto);
            $stmt_vendedor->execute();
            $result_vendedor = $stmt_vendedor->get_result();
            $id_usuario_vendedor = $result_vendedor->fetch_assoc()['ID_USUARIO'];

            $query_transaccion = "
                INSERT INTO Transacción (HoraFechaTransaccion, CantidadComprada, PrecioTotalProducto, ID_USUARIO_CLIENTE, ID_USUARIO_VENDEDOR, ID_PRODUCTO)
                VALUES (NOW(), ?, ?, ?, ?, ?)";
            $stmt_transaccion = $conn->prepare($query_transaccion);
            $stmt_transaccion->bind_param("idiii", $cantidad_comprada, $precio_total_producto, $id_usuario_cliente, $id_usuario_vendedor, $id_producto);
            $stmt_transaccion->execute();
            // Obtener el ID de la transacción recién creada
            $id_transaccion = $conn->insert_id;

            // Guarda el ID de la transacción en la sesión
            $_SESSION['transacciones_pendientes'][] = $id_transaccion;

            $query_update_producto = "UPDATE Producto SET CantidadProducto = CantidadProducto - ? WHERE ID_PRODUCTO = ?";
            $stmt_update_producto = $conn->prepare($query_update_producto);
            $stmt_update_producto->bind_param("ii", $cantidad_comprada, $id_producto);
            $stmt_update_producto->execute();

            // Eliminar el producto del carrito
            $query_delete_producto_carrito = "DELETE FROM ProductoEnCarrito WHERE ID_CARRITO = ? AND ID_PRODUCTO = ?";
            $stmt_delete_producto_carrito = $conn->prepare($query_delete_producto_carrito);
            $stmt_delete_producto_carrito->bind_param("ii", $producto['ID_CARRITO'], $id_producto);
            $stmt_delete_producto_carrito->execute();
        }

        // Actualizar el carrito total
        $query_update_carrito = "UPDATE Carrito SET PrecioTotal = 0 WHERE ID_USUARIO = ?";
        $stmt_update_carrito = $conn->prepare($query_update_carrito);
        $stmt_update_carrito->bind_param("i", $id_usuario_cliente);
        $stmt_update_carrito->execute();

        $conn->commit(); 

        // Guarda los productos comprados en la sesión
        $_SESSION['productos_comprados'] = $productos_carrito;

        echo json_encode(['status' => 'success', 'message' => 'Compra realizada con éxito.']);
    } catch (Exception $e) {
        $conn->rollback(); 
        echo json_encode(['status' => 'error', 'message' => 'Error al procesar la compra.']);
    }
}
?>
