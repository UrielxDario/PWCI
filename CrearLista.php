<?php
session_start();
require 'conexionBaseDeDatos.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id_usuario'];
    
    $nombreLista = mysqli_real_escape_string($conn, $_POST['nombreLista']);
    $descripcionLista = mysqli_real_escape_string($conn, $_POST['descripcionLista']);
    $privacidadLista = mysqli_real_escape_string($conn, $_POST['privacidadLista']); 

    $query = "INSERT INTO Lista (NombreLista, DescripcionLista, PrivacidadLista, ID_USUARIO) VALUES ('$nombreLista', '$descripcionLista', '$privacidadLista', '$user_id')";
    $resultado = mysqli_query($conn, $query);

    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error en la inserción']);
    }
}
?>
