<?php

$username = $_GET['username'];

include '../conexionBaseDeDatos.php';

$sql = "
    SELECT 
        Producto.ID_PRODUCTO,
        Producto.NombreProducto,
        Producto.PrecioProducto,
        Multimedia.Archivo AS ImgProducto -- Obtenemos una sola imagen por producto
    FROM 
        Producto
    JOIN 
        Usuario ON Producto.ID_ADMIN = Usuario.ID_USUARIO
    JOIN 
        Multimedia ON Multimedia.ID_PRODUCTO = Producto.ID_PRODUCTO
    WHERE 
        Usuario.Username = ? AND 
        Producto.AutorizacionAdmin = 'Si'
    GROUP BY 
        Producto.ID_PRODUCTO, Producto.NombreProducto, Producto.PrecioProducto
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // Convertimos la imagen binaria a base64
    $imgBase64 = base64_encode($row['ImgProducto']);
    $imgSrc = 'data:image/jpeg;base64,' . $imgBase64;

    $products[] = [
        'NombreProducto' => $row['NombreProducto'],
        'PrecioProducto' => $row['PrecioProducto'],
        'ImgProducto' => $imgSrc
    ];
}

echo json_encode($products);

$stmt->close();
$conn->close();
?>
