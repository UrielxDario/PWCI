<?php
include '../conexionBaseDeDatos.php';
// Asumimos que el parámetro 'username' es pasado desde el JavaScript
$username = $_GET['username'];



$sql = "
    SELECT
        Lista.ID_LISTA,
        Lista.NombreLista,
        Lista.DescripcionLista,
        Lista.PrivacidadLista,
        Producto.NombreProducto,
        Producto.PrecioProducto,
        Multimedia.Archivo AS ImgProducto
    FROM
        Lista
    JOIN
        ProductoEnLista ON ProductoEnLista.ID_LISTA = Lista.ID_LISTA
    JOIN
        Producto ON ProductoEnLista.ID_PRODUCTO = Producto.ID_PRODUCTO
    JOIN
        Multimedia ON Multimedia.ID_PRODUCTO = Producto.ID_PRODUCTO
    WHERE
        Lista.ID_USUARIO = (SELECT ID_USUARIO FROM Usuario WHERE Username = ?)
    GROUP BY producto.NombreProducto
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

$lists = [];
while ($row = $result->fetch_assoc()) {
    $list_id = $row['ID_LISTA'];
    
    // Si no existe la lista en el arreglo, la agregamos
    if (!isset($lists[$list_id])) {
        $lists[$list_id] = [
            'ID_LISTA' => $row['ID_LISTA'],
            'NombreLista' => $row['NombreLista'],
            'DescripcionLista' => $row['DescripcionLista'],
            'PrivacidadLista' => $row['PrivacidadLista'],
            'productos' => []
        ];
    }
    // Convertir la imagen en binario a base64
    $imgBase64 = base64_encode($row['ImgProducto']);
    $imgSrc = 'data:image/jpeg;base64,' . $imgBase64;

    // Agregar el producto a la lista correspondiente
    $lists[$list_id]['productos'][] = [
        'NombreProducto' => $row['NombreProducto'],
        'PrecioProducto' => $row['PrecioProducto'],
        'ImgProducto' => $imgSrc
    ];
}

echo json_encode(array_values($lists));

?>
