<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action']) && $data['action'] === 'eliminar_productos') {
        unset($_SESSION['productos_comprados']);
        unset($_SESSION['transacciones_pendientes']); 

        error_log("Productos y transacciones eliminados exitosamente.");
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}

?>
