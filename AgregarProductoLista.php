<?php
include 'conexionBaseDeDatos.php';  

$id_lista = $_POST['id_lista'];
$id_producto = $_POST['id_producto'];

if (!empty($id_lista) && !empty($id_producto)) {
    $query = "INSERT INTO ProductoEnLista (ID_LISTA, ID_PRODUCTO) VALUES (?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $id_lista, $id_producto);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al insertar el producto']);
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
