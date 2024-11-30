<?php
session_start();
require 'conexionBaseDeDatos.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id_usuario'];
    
    $nombreCategoria = mysqli_real_escape_string($conn, $_POST['nombreCategoria']);
    $descripcionCategoria = mysqli_real_escape_string($conn, $_POST['descripcionCategoria']);

    $query = "INSERT INTO categoría (NombreCategoria, DescripcionCategoria, ID_USUARIO) VALUES ('$nombreCategoria', '$descripcionCategoria', '$user_id')";
    $resultado = mysqli_query($conn, $query);

    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error en la inserción']);
    }
}
?>
