<?php
require 'conexionBaseDeDatos.php';

if (!isset($_GET['id_lista'])) {
    echo json_encode(['error' => 'ID de lista no proporcionado']);
    exit();
}

$id_lista = intval($_GET['id_lista']);

$sql = "SELECT 
            pl.ID_ProductoEnLista AS ID_ProductoEnLista,
            p.NombreProducto AS NombreProducto, 
            p.DescripcionProducto AS DescripcionProducto, 
            p.PrecioProducto AS PrecioProducto
        FROM ProductoEnLista pl
        INNER JOIN Producto p ON pl.ID_PRODUCTO = p.ID_PRODUCTO
        WHERE pl.ID_LISTA = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_lista);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
?>
