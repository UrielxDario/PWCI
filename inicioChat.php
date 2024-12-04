<?php
include 'conexionBaseDeDatos.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_cliente = $_SESSION['id_usuario']; // El ID del usuario logueado (cliente)
$id_vendedor = $_GET['id_vendedor']; // El ID del vendedor desde la URL

// Verificar si ya existe un chat entre el cliente y el vendedor
$query = "SELECT * FROM Chat WHERE ID_Cliente = ? AND ID_Vendedor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $id_cliente, $id_vendedor);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si ya existe, redirige al chat
    $chat = $result->fetch_assoc();
    header("Location: chat.php?id_chat=" . $chat['ID_Chat']);
    exit();
} else {
    // Crear un nuevo chat
    $query = "INSERT INTO Chat (ID_Cliente, ID_Vendedor) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $id_cliente, $id_vendedor);
    $stmt->execute();
    
    // Redirige al nuevo chat
    $new_chat_id = $stmt->insert_id;
    header("Location: chat.php?id_chat=" . $new_chat_id);
    exit();
}
?>
