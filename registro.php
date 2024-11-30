<?php

require 'conexionBaseDeDatos.php';


if (isset($_POST['submit'])) {
    date_default_timezone_set('America/Mexico_City');
   /* $correo = $_POST['email'];*/
   $correo = $_POST['correoCompleto'];

    $username = $_POST['username'];
    $contrasena = $_POST['password'];
    $rol = $_POST['role'];
    $sexo = $_POST['gender'];
    $fechaNacimiento = $_POST['birthdate'];
    $fechaIngresoUsuario = date('Y-m-d H:i:s'); 
    $imgPerfil = $_FILES['avatar']['name']; 
    $apellidoPaterno = $_POST['apellido_paterno'];
    $apellidoMaterno = $_POST['apellido_materno'];
    $nombre = $_POST['nombres']; 

    $recordarUsuario = null; 

    $privacidadUsuario = null;
    if ($rol === 'Cliente') {
        if (isset($_POST['privacidad'])) {
            $privacidadUsuario = $_POST['privacidad'];
        }
    }


    $autorizacionAdmin = ($rol === 'Administrador') ? 'No' : 'Si';


    $consultaCorreo = "SELECT * FROM Usuario WHERE Correo = '$correo'";
    $resultadoCorreo = mysqli_query($conn, $consultaCorreo);
    
    if (mysqli_num_rows($resultadoCorreo) > 0) {
        echo "<script>alert('El correo ya está en uso.');</script>";
    } else {

        $consultaUsuario = "SELECT * FROM Usuario WHERE Username = '$username'";
        $resultadoUsuario = mysqli_query($conn, $consultaUsuario);
        
        if (mysqli_num_rows($resultadoUsuario) > 0) {
            echo "<script>alert('El nombre de usuario ya está en uso.');</script>";
        } else {
            $insertarRegistroUsuario = "INSERT INTO Usuario (Correo, Username, Contraseña, Rol, Sexo, FechaNacimiento, FechaIngresoUsuario, ImgPerfil, PrivacidadUsuario, RecordarUsuario, EstatusUsuario, ApellidoPaterno, ApellidoMaterno, Nombre, AutorizacionAdmin) 
                                        VALUES ('$correo', '$username', '$contrasena', '$rol', '$sexo', '$fechaNacimiento', '$fechaIngresoUsuario', '$imgPerfil', '$privacidadUsuario', '$recordarUsuario', 'activo', '$apellidoPaterno', '$apellidoMaterno', '$nombre', '$autorizacionAdmin')";

            $EXECinsertarRegistroUsuario = mysqli_query($conn, $insertarRegistroUsuario);

            if ($EXECinsertarRegistroUsuario) {
                echo "Registro insertado correctamente.";
                header("Location: login.php"); 
                exit(); 
            } else {
                echo "<script>alert('Error al insertar el registro.');</script>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - The Dark Wardrobe</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlace a tu archivo de estilos CSS -->
    <link href="estilos/styles.css" rel="stylesheet">
    <style>
        body {
            background-color: #1A1A1A; 
        }
    </style>
</head>
<body>
    <!-- Sección de registro -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-6"> 
            <div class="card bg-dark text-white p-4">
                <h2 class="text-center mb-4">Registro de Usuario</h2>
                <form action="#" method="post" enctype="multipart/form-data">
                    
                    <!-- Campo de Correo Electrónico -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="email" name="email" placeholder="Ingresa tu correo">
                            <select id="Dominio" class="form-select">
                                <option value="@gmail.com">@gmail.com</option>
                                <option value="@hotmail.com">@hotmail.com</option>
                                <option value="@outlook.com">@outlook.com</option>
                                <option value="@yahoo.com">@yahoo.com</option>
                            </select>
                            <input type="hidden" id="correoCompleto" name="correoCompleto">

                        </div>
                    </div>

                    <!-- Campo de Nombre de Usuario -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario (mínimo 3 caracteres):</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu nombre de usuario" >
                    </div>

                    <!-- Campo de Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña (Mínimo 8 caracteres, incluyendo mayúscula, minúscula, número y carácter especial):</label>
                        <input type="text" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" >
                    </div>

                    <!-- Campo de Rol de Usuario -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol de Usuario:</label>
                        <select class="form-control" id="role" name="role" >
                            <option value="">Selecciona un rol</option>
 
                            <option value="Cliente">Cliente</option>
                            <option value="Vendedor">Vendedor</option>
                            <option value="Administrador">Administrador</option>
                        </select>
                    </div>

                    <!-- Campo que se abre solo si se selecciono Cliente como rol-->
                    <div id="privacidadDiv" class="mb-3" style="display: none;">
                        <label class="form-label">Privacidad de Perfil:</label><br>
                        <input type="radio" id="publico" name="privacidad" value="publico" >
                        <label for="publico">Público</label><br>
                        <input type="radio" id="privado" name="privacidad" value="privado" >
                        <label for="privado">Privado</label><br>
                    </div>

                    <!-- Campo de Imagen Avatar -->
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Imagen de Avatar:</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="img/*" >


                        <div id="ContainerAvatarRegistro" class="mt-3">
                            <img id="ImagenAvatar" src="#" alt="Vista previa de la imagen" style="display: none;">
                        </div>
                    </div>

                    <!-- Campo de Apellido Paterno -->
                    <div class="mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido Paterno:</label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" placeholder="Ingresa tu apellido paterno" >
                    </div>

                    <!-- Campo de Apellido Materno -->
                    <div class="mb-3">
                        <label for="apellido_materno" class="form-label">Apellido Materno:</label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" placeholder="Ingresa tu apellido materno" >
                    </div>

                    <!-- Campo de Nombres -->
                    <div class="mb-3">
                        <label for="nombres" class="form-label">Nombre(s):</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Ingresa tus nombres" >
                    </div>

                    <!-- Campo de Fecha de Nacimiento -->
                    <div class="mb-3">
                        <label for="birthdate" class="form-label">Fecha de Nacimiento:</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" >
                    </div>

                    <!-- Campo de Sexo -->
                    <div class="mb-3">
                        <label for="gender" class="form-label">Sexo:</label>
                        <select class="form-control" id="gender" name="gender" >
                            <option value="">Selecciona tu sexo</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- Botón de Registro -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning" name="submit">Registrarse</button>
                    </div>

                    <!-- Botón para volver al inicio de sesión -->
                    <div class="text-center">
                        <a href="login.php" class="btn btn-outline-light">¿Ya tienes cuenta? Inicia sesión</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!--PARA VALIDACIONES -->
    <script src ="registro.js"></script> 
   

</body>
</html>
