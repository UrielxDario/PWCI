<?php
session_start();
require 'conexionBaseDeDatos.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

$user_id = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreLista = mysqli_real_escape_string($conn, $_POST['nombreLista']);
    $descripcionLista = mysqli_real_escape_string($conn, $_POST['descripcionLista']);
    $privacidadLista = mysqli_real_escape_string($conn, $_POST['privacidadLista']);

    $query = "INSERT INTO Lista (NombreLista, DescripcionLista, PrivacidadLista, ID_USUARIO) VALUES ('$nombreLista', '$descripcionLista', '$privacidadLista', '$user_id')";
    $resultado = mysqli_query($conn, $query);

    echo json_encode(['success' => $resultado]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT ID_LISTA, NombreLista, PrivacidadLista FROM Lista WHERE ID_USUARIO = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $listas = [];
    while ($row = $result->fetch_assoc()) {
        $listas[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($listas);
    exit();
}
?>
