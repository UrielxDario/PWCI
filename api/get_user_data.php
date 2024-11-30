<?php
// Asumiendo que ya tienes la sesión iniciada y puedes obtener el ID del usuario
session_start();
$user_id = $_SESSION['id_usuario'];

include '../conexionBaseDeDatos.php'; // Archivo que conecta con la base de datos

$sql = "SELECT Correo, Username, Contraseña, ApellidoPaterno, ApellidoMaterno, Nombre, FechaNacimiento, Sexo, Rol, ImgPerfil, PrivacidadUsuario FROM usuario WHERE ID_USUARIO = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'No se encontraron datos para este usuario.']);
}

$stmt->close();
$conn->close();
?>

