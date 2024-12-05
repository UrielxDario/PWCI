<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false]);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id_producto'], $data['cantidad']) || $data['cantidad'] < 1) {
    echo json_encode(['success' => false]);
    exit();
}

require 'conexionBaseDeDatos.php';

$queryProducto = "SELECT CantidadProducto, PrecioProducto FROM Producto WHERE ID_PRODUCTO = ?";
$stmtProducto = $conn->prepare($queryProducto);
$stmtProducto->bind_param("i", $data['id_producto']);
$stmtProducto->execute();
$resultProducto = $stmtProducto->get_result();

if ($resultProducto->num_rows === 0) {
    echo json_encode(['success' => false]);
    exit();
}

$rowProducto = $resultProducto->fetch_assoc();
if ($data['cantidad'] > $rowProducto['CantidadProducto']) {
    echo json_encode(['success' => false, 'message' => 'Cantidad no disponible']);
    exit();
}

$queryUpdate = "UPDATE ProductoEnCarrito SET CantidadAgregada = ? WHERE ID_PRODUCTO = ? AND ID_CARRITO = (
    SELECT ID_CARRITO FROM Carrito WHERE ID_USUARIO = ? AND EstadoCarrito = 'activo'
)";
$stmtUpdate = $conn->prepare($queryUpdate);
$stmtUpdate->bind_param("iii", $data['cantidad'], $data['id_producto'], $id_usuario);
$success = $stmtUpdate->execute();

$queryTotal = "UPDATE Carrito 
               SET PrecioTotal = (
                   SELECT SUM(CantidadAgregada * PrecioProducto) 
                   FROM ProductoEnCarrito 
                   JOIN Producto ON ProductoEnCarrito.ID_PRODUCTO = Producto.ID_PRODUCTO 
                   WHERE ProductoEnCarrito.ID_CARRITO = Carrito.ID_CARRITO
               )
               WHERE ID_USUARIO = ? AND EstadoCarrito = 'activo'";
$stmtTotal = $conn->prepare($queryTotal);
$stmtTotal->bind_param("i", $id_usuario);
$stmtTotal->execute();

echo json_encode(['success' => $success]);
?>
