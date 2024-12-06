<?php
require 'conexionBaseDeDatos.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Datos JSON inválidos.']);
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!empty($data['productos'])) {
    foreach ($data['productos'] as $producto) {
        $id_transaccion = $producto['idTransaccion'];
        $reseña = $producto['reseña'];
        $gusto = $producto['gusto'];

        $query_producto = "
            SELECT t.ID_PRODUCTO
            FROM Transacción t
            WHERE t.ID_TRANSACCION = ?";
        
        $stmt_producto = $conn->prepare($query_producto);
        $stmt_producto->bind_param("i", $id_transaccion);
        $stmt_producto->execute();
        $result_producto = $stmt_producto->get_result();
        $row_producto = $result_producto->fetch_assoc();
        $id_producto = $row_producto['ID_PRODUCTO'];

        $query_insert_comentario = "
            INSERT INTO Comentario (Comentario, Calificación, ID_USUARIO, ID_PRODUCTO, ID_TRANSACCION)
            SELECT ?, ?, t.ID_USUARIO_CLIENTE, ?, t.ID_TRANSACCION
            FROM Transacción t
            WHERE t.ID_TRANSACCION = ?";

        $stmt_comentario = $conn->prepare($query_insert_comentario);
        $stmt_comentario->bind_param("ssii", $reseña, $gusto, $id_producto, $id_transaccion);
        $stmt_comentario->execute();

        // Calcular el promedio de calificación del producto
        $gusto_valor = ($gusto == 'Me gustó') ? 1 : 0;

        // Obtener todas las calificaciones del producto
        $query_calificaciones = "
            SELECT Calificación
            FROM Comentario
            WHERE ID_PRODUCTO = ?";

        $stmt_calificaciones = $conn->prepare($query_calificaciones);
        $stmt_calificaciones->bind_param("i", $id_producto);
        $stmt_calificaciones->execute();
        $result = $stmt_calificaciones->get_result();

        $total_calificaciones = 0;
        $cantidad_calificaciones = 0;

        while ($row = $result->fetch_assoc()) {
            $cantidad_calificaciones++;
            if ($row['Calificación'] == 'Me gustó') {
                $total_calificaciones++;
            }
        }

        // Calcular el promedio en porcentaje
        if ($cantidad_calificaciones > 0) {
            $promedio = ($total_calificaciones / $cantidad_calificaciones) * 100;
        } else {
            $promedio = 0; // Si no hay calificaciones, el promedio es 0
        }

        // Actualizar el promedio en la tabla Producto
        $query_actualizar_promedio = "
            UPDATE Producto
            SET PromedioCalificacion = ?
            WHERE ID_PRODUCTO = ?";

        $stmt_actualizar = $conn->prepare($query_actualizar_promedio);
        $stmt_actualizar->bind_param("di", $promedio, $id_producto);
        $stmt_actualizar->execute();
    }

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se enviaron productos para calificar.']);
}
?>
