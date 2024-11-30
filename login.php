<?php

require 'conexionBaseDeDatos.php';

$error = '';

session_start(); // Inicia la sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['usuario'])) {
        $usuario = $_POST['usuario']; 
        $contrasena = !empty($_POST['password']) ? $_POST['password'] : null;
        
        $consultalogin = "SELECT * FROM Usuario WHERE (Correo = '$usuario' OR Username = '$usuario') AND EstatusUsuario = 'activo'";
        $EXECconsultalogin = mysqli_query($conn, $consultalogin);

        if ($EXECconsultalogin && mysqli_num_rows($EXECconsultalogin) > 0) {
            $fila = mysqli_fetch_assoc($EXECconsultalogin);

            if ($fila['RecordarUsuario'] === 'Si') {
                $_SESSION['id_usuario'] = $fila['ID_USUARIO'];
                $_SESSION['usuario'] = $fila['Username'];
                $_SESSION['rol_usuario'] = $fila['Rol'];
                
                header("Location: MiddleWareRoles.php");
                exit();
            } elseif (!empty($contrasena) && $contrasena === $fila['Contraseña']) {
                $_SESSION['id_usuario'] = $fila['ID_USUARIO'];
                $_SESSION['usuario'] = $fila['Username'];
                $_SESSION['rol_usuario'] = $fila['Rol'];

                if (isset($_POST['RecordarUsuario']) && $_POST['RecordarUsuario'] === 'Si') {
                    $id_usuario = $fila['ID_USUARIO'];  
                    $actualizarRecordar = "UPDATE Usuario SET RecordarUsuario = 'Si' WHERE ID_USUARIO = '$id_usuario'";
                    mysqli_query($conn, $actualizarRecordar);
                }

                header("Location: MiddleWareRoles.php");
                exit();
            } else {
                $error = 'La contraseña es incorrecta.'; 
            }
        } else {
            $error = 'El usuario no existe o está inactivo.';  
        }
    } else {
        $error = 'Por favor, llena el campo de usuario.';  
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - The Dark Wardrobe</title>
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
    <!-- Sección de login -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-4"> 
            <div class="card bg-dark text-white p-4">
                <h2 class="text-center mb-4">Inicio de Sesión</h2>


                   <!-- Si hay un error, lo muestra en la alerta -->
                   <?php if(!empty($error)): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                
                <form action="#" method="post">
                    <!-- Campo de usuario o correo -->
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario o Correo:</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingresa tu usuario o correo" >
                    </div>
                    
                    <!-- Campo de contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" >
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="RecordarUsuario" name="RecordarUsuario" value="Si">
                        <label class="form-check-label" for="Recordar">Recuérdame</label>
                    </div>
                    
                    <!-- Botón de Iniciar Sesión -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning">Iniciar Sesión</button>
                    </div>

                    
                    <!-- Botón de Registro -->
                    <div class="text-center">
                        <a href="registro.php" class="btn btn-outline-light">¿No tienes cuenta? Regístrate</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
