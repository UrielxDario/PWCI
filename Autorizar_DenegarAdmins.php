<?php
require 'conexionBaseDeDatos.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action == 'autorizar') {
        $query = "UPDATE Usuario SET AutorizacionAdmin = 'Si' WHERE Id_Usuario = ?";
        $message = "Usuario autorizado exitosamente.";
    } elseif ($action == 'denegar') {
        $query = "UPDATE Usuario SET EstatusUsuario = 'inactivo' WHERE Id_Usuario = ?";
        $message = "Usuario denegado exitosamente.";
    } else {
        die("Acción no válida.");
    }

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('$message'); window.location.href='AutorizarAdministradores.php';</script>";
        } else {
            echo "<script>alert('Error al actualizar el usuario.'); window.location.href='AutorizarAdministradores.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error en la preparación de la consulta.'); window.location.href='AutorizarAdministradores.php';</script>";
    }
} else {
    echo "<script>alert('Datos no válidos.'); window.location.href='AutorizarAdministradores.php';</script>";
}

$conn->close();
?>
