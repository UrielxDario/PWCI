<?php
include 'conexionBaseDeDatos.php';

$id_producto = $_POST['id_producto']; 
$id_carrito = $_POST['id_carrito'];  
$id_usuario = $_POST['id_usuario'];  

// Agrega logs para depurar
error_log("id_producto: $id_producto, id_carrito: $id_carrito, id_usuario: $id_usuario");

if (!empty($id_producto) && !empty($id_carrito) && !empty($id_usuario)) {
    $query_delete = "DELETE FROM ProductoEnCarrito WHERE ID_Producto = ? AND ID_CARRITO = ?";

    if ($stmt = $conn->prepare($query_delete)) {
        $stmt->bind_param("ii", $id_producto, $id_carrito);

        if ($stmt->execute()) {
            $stmt->close(); 
            
            $query_total = "SELECT SUM(p.PrecioProducto * pc.CantidadAgregada) AS total_carrito
                            FROM Producto p
                            JOIN ProductoEnCarrito pc ON p.ID_PRODUCTO = pc.ID_PRODUCTO
                            WHERE pc.ID_CARRITO = ?";

            if ($stmt_total = $conn->prepare($query_total)) {
                $stmt_total->bind_param("i", $id_carrito);
                $stmt_total->execute();
                
                $stmt_total->store_result(); 
                $stmt_total->bind_result($total_carrito); 
                $stmt_total->fetch();  

                error_log("Total calculado del carrito: $total_carrito");

                if ($total_carrito === null) {
                    $total_carrito = 0;
                }

                $query_update_total = "UPDATE Carrito SET PrecioTotal = ? WHERE ID_CARRITO = ?";
                if ($stmt_update = $conn->prepare($query_update_total)) {
                    $stmt_update->bind_param("di", $total_carrito, $id_carrito);
                    $stmt_update->execute();
                    $stmt_update->close(); 
                }

                echo json_encode(['success' => true, 'total' => $total_carrito]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al calcular el total']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
}

$conn->close();
?>
