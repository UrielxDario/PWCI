<?php
session_start();
include 'conexionBaseDeDatos.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents('php://input'), true);

$id_producto = $data['id_producto'];
$cantidad = $data['cantidad'];

if (!$id_producto || !$cantidad || $cantidad <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit();
}

$conn->begin_transaction();

try {
    $query = "SELECT ID_CARRITO, PrecioTotal FROM Carrito WHERE ID_USUARIO = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $carrito = $result->fetch_assoc();
        $id_carrito = $carrito['ID_CARRITO'];
        $precio_total_actual = $carrito['PrecioTotal'];
    } else {
        $query = "INSERT INTO Carrito (PrecioTotal, EstadoCarrito, ID_USUARIO) VALUES (0, 'Activo', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $id_carrito = $conn->insert_id;
        $precio_total_actual = 0;
    }

    $query = "SELECT PrecioProducto FROM Producto WHERE ID_PRODUCTO = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Producto no encontrado.');
    }

    $producto = $result->fetch_assoc();
    $precio_producto = $producto['PrecioProducto'];

    $query = "INSERT INTO ProductoEnCarrito (CantidadAgregada, ID_CARRITO, ID_PRODUCTO) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $cantidad, $id_carrito, $id_producto);
    $stmt->execute();

    $nuevo_precio_total = $precio_total_actual + ($cantidad * $precio_producto);
    $query = "UPDATE Carrito SET PrecioTotal = ? WHERE ID_CARRITO = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("di", $nuevo_precio_total, $id_carrito);
    $stmt->execute();

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
