<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

$rol = $_SESSION['rol_usuario'];  
$usuario = $_SESSION['usuario']; // Aquí es donde tienes el email o username

require 'conexionBaseDeDatos.php';

// Suponiendo que 'usuario' es el email o username, debes obtener el user_id
$query = "SELECT Id_Usuario FROM Usuario WHERE Correo = ? OR Username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $usuario, $usuario); // 'ss' para string (email o username)
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    // Si no se encuentra el usuario, redirige o muestra error
    echo "No se encontró el usuario.";
    exit();
}

// Ahora, con el user_id, puedes proceder con la consulta original
$query = "SELECT AutorizacionAdmin FROM Usuario WHERE Id_Usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($autorizacionAdmin);
$stmt->fetch();
$stmt->close();

// Verifica el valor de AutorizacionAdmin
var_dump($autorizacionAdmin);

$autorizacionAdmin = trim($autorizacionAdmin); // Elimina espacios en blanco

switch ($rol) {
    case 'SuperAdministrador':
        header('Location: AutorizarAdministradores.php');
        exit();
    case 'Administrador':
        if ($autorizacionAdmin === 'Si') {
            header('Location: AutorizarProductos.php');
        } else {
            header('Location: EsperandoAutorizacionAdmin.php');
        }
        exit();
    case 'Vendedor':
        header('Location: home.php');
        exit();
    case 'Cliente':
        header('Location: home.php');
        exit();
}

$conn->close();
?>
