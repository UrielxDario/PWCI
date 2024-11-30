<?php
require 'conexionBaseDeDatos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = [];

    // Verificación para correo
    if (isset($_POST['correo'])) {
        $correo = $_POST['correo'];
        $consultaCorreo = "SELECT ID_USUARIO FROM Usuario WHERE Correo = '$correo'";
        $resultadoCorreo = mysqli_query($conn, $consultaCorreo);

        if (mysqli_num_rows($resultadoCorreo) > 0) {
            $usuario = mysqli_fetch_assoc($resultadoCorreo);
            $respuesta = ['existe' => true, 'usuarioId' => $usuario['ID_USUARIO']];
        } else {
            $respuesta = ['existe' => false];
        }
    } 
    // Verificación para nombre de usuario
    elseif (isset($_POST['username'])) {
        $username = $_POST['username'];
        $consultaUsuario = "SELECT ID_USUARIO FROM Usuario WHERE Username = '$username'";
        $resultadoUsuario = mysqli_query($conn, $consultaUsuario);

        if (mysqli_num_rows($resultadoUsuario) > 0) {
            $usuario = mysqli_fetch_assoc($resultadoUsuario);
            $respuesta = ['existe' => true, 'usuarioId' => $usuario['ID_USUARIO']];
        } else {
            $respuesta = ['existe' => false];
        }
    }

    echo json_encode($respuesta);
}
?>