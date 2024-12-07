<?php
session_start(); 

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}


$id_usuario = $_SESSION['id_usuario'];
require 'conexionBaseDeDatos.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['action'])) {
    $product_id = intval($_POST['product_id']);  
    $action = $_POST['action'];  

   
    if ($action === 'autorizar') {
        $query = "UPDATE Producto SET AutorizacionAdmin = 'Si', ID_ADMIN = ? WHERE ID_PRODUCTO = ?";
    } 
    
    elseif ($action === 'denegar') {
        $query = "UPDATE Producto SET AutorizacionAdmin = 'No' WHERE ID_PRODUCTO = ?";
    } else {
        die('Acción no válida.');
    }

    // Prepara y ejecuta la consulta
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param('ii', $id_usuario, $product_id); // Vincula el ID del producto
        if ($stmt->execute()) {
            // Redirige de nuevo a la página de autorización con un mensaje
            header('Location: AutorizarProductos.php?mensaje=exito');
            exit();
        } else {
            die('Error al ejecutar la consulta.');
        }
    } else {
        die('Error al preparar la consulta.');
    }
}

// Cierra la conexión
$conn->close();
die('Solicitud no válida.');
?>