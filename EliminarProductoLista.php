<?php
include 'conexionBaseDeDatos.php';

$id_lista = $_POST['id_lista'];
$id_producto_en_lista = $_POST['id_producto_en_lista'];

error_log("ID Lista: " . $id_lista); 
error_log("ID Producto en Lista: " . $id_producto_en_lista);

if (!empty($id_lista) && !empty($id_producto_en_lista)) {
    $query = "DELETE FROM ProductoEnLista WHERE ID_ProductoEnLista = ? AND ID_LISTA = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $id_producto_en_lista, $id_lista);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
}

$conn->close();
?>
