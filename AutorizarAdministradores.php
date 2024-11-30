<?php

require 'conexionBaseDeDatos.php';

$query = "SELECT Id_Usuario, Username, ImgPerfil FROM usuario WHERE Rol = 'Administrador' AND AutorizacionAdmin = 'No' AND EstatusUsuario= 'activo'";
$resultado = $conn->query($query);

$usuariosPendientes = []; 
if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $usuariosPendientes[] = $row; 
    }
}

// Cierra la conexión
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorizar Administradores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/AutorizarAdministradores.css" rel="stylesheet">
</head>
<body>
   <!-- Nav bar -->
   <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" >The Dark Wardrobe</a>
            
           

            <!-- Iconos de cuenta y carrito -->
            <ul class="navbar-nav">
                <!-- Cuenta y listas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/user.png" alt="Cuenta" width="20"> Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                       
                       
                    <li><a class="dropdown-item" href="CerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>

                
            </ul>
        </div>
    </nav>




    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2>Usuarios Faltantes Por Autorizar Rol De Administrador</h2>

                <?php if (!empty($usuariosPendientes)) : ?>
                    <?php foreach ($usuariosPendientes as $usuario) : ?>
                        <div class="container mb-4 administrador">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="img/<?php echo htmlspecialchars($usuario['ImgPerfil']) ?: 'user.jpg'; ?>" class="img-fluid imgadmin" alt="Admin <?php echo htmlspecialchars($usuario['Username']); ?>">
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?php echo htmlspecialchars($usuario['Username']); ?></h4>
                                        </div>
                                        <div class="text-end">
                                            <!-- Formulario para autorizar usuario -->
                                            <form action="Autorizar_DenegarAdmins.php" method="post" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['Id_Usuario']; ?>">
                                                <button type="submit" name="action" value="autorizar" class="btn btn-success">Autorizar Usuario</button>
                                            </form>

                                            <!-- Formulario para denegar usuario -->
                                            <form action="Autorizar_DenegarAdmins.php" method="post" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['Id_Usuario']; ?>">
                                                <button type="submit" name="action" value="denegar" class="btn btn-danger ms-2">Denegar Usuario</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No hay usuarios pendientes de autorización.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
   
</body>
</html>
