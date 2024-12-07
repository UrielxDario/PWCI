<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id_usuario'];
$rol_usuario = $_SESSION['rol_usuario'];

require 'conexionBaseDeDatos.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'baja') {
    // Actualizar el estatus del usuario a "inactivo"
    $consultaBaja = "UPDATE Usuario SET EstatusUsuario = 'inactivo' WHERE Id_Usuario = '$user_id'";
    $resultadoBaja = mysqli_query($conn, $consultaBaja);

    if ($resultadoBaja) {
        echo "<script>alert('Tu cuenta ha sido dada de baja.'); window.location.href = 'login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Ocurrió un error al intentar desactivar tu cuenta. Inténtalo nuevamente.');</script>";
    }
}


if (isset($_POST['GuardarCambios'])) {
    date_default_timezone_set('America/Mexico_City');
    
    // Recoger datos del formulario
    $correo = isset($_POST['correoCompleto']) ? $_POST['correoCompleto'] : '';
    $username = $_POST['username'];
    $contrasena = $_POST['password'];
    $rol = $_POST['role'];
    $sexo = $_POST['gender'];
    $fechaNacimiento = $_POST['birthdate'];
    $imgPerfil = $_FILES['avatar']['name']; 
    $apellidoPaterno = $_POST['apellido_paterno'];
    $apellidoMaterno = $_POST['apellido_materno'];
    $nombre = $_POST['nombres']; 
    $privacidadUsuario = $rol === 'Cliente' && isset($_POST['privacidad']) ? $_POST['privacidad'] : null;
    $autorizacionAdmin = ($rol === 'Administrador') ? 'No' : 'Si';

    // Guardar los valores actuales del usuario (que se obtienen de la base de datos)
    $consultaUsuarioActual = "SELECT * FROM Usuario WHERE Id_Usuario = '$user_id'";
    $resultadoUsuarioActual = mysqli_query($conn, $consultaUsuarioActual);
    $usuarioActual = mysqli_fetch_assoc($resultadoUsuarioActual);

    // Iniciar un array para almacenar los cambios en la consulta
    $camposActualizar = [];

    // Comparar valores actuales con los nuevos, y agregar solo los que han cambiado
    if ($correo != $usuarioActual['Correo'] && !empty($correo)) {
        $camposActualizar[] = "Correo = '$correo'";
    }
    
    if ($username != $usuarioActual['Username']) {
        $camposActualizar[] = "Username = '$username'";
    }
    if ($contrasena != $usuarioActual['Contraseña']) {
        $camposActualizar[] = "Contraseña = '$contrasena'";
    }
    if ($rol != $usuarioActual['Rol']) {
        $camposActualizar[] = "Rol = '$rol'";
    }
    if ($sexo != $usuarioActual['Sexo']) {
        $camposActualizar[] = "Sexo = '$sexo'";
    }
    if ($fechaNacimiento != $usuarioActual['FechaNacimiento']) {
        $camposActualizar[] = "FechaNacimiento = '$fechaNacimiento'";
    }
    if ($imgPerfil && $imgPerfil != $usuarioActual['ImgPerfil']) {
        $camposActualizar[] = "ImgPerfil = '$imgPerfil'";
        // Aquí puedes mover la nueva imagen al servidor
        $rutaDestino = 'img/' . $imgPerfil;
        move_uploaded_file($_FILES['avatar']['tmp_name'], $rutaDestino);
    }
    if ($apellidoPaterno != $usuarioActual['ApellidoPaterno']) {
        $camposActualizar[] = "ApellidoPaterno = '$apellidoPaterno'";
    }
    if ($apellidoMaterno != $usuarioActual['ApellidoMaterno']) {
        $camposActualizar[] = "ApellidoMaterno = '$apellidoMaterno'";
    }
    if ($nombre != $usuarioActual['Nombre']) {
        $camposActualizar[] = "Nombre = '$nombre'";
    }
    if ($privacidadUsuario != $usuarioActual['PrivacidadUsuario']) {
        $camposActualizar[] = "PrivacidadUsuario = '$privacidadUsuario'";
    }

    // Si hay cambios, construir la consulta de actualización
    if (count($camposActualizar) > 0) {
        $consultaActualizar = "UPDATE Usuario SET " . implode(", ", $camposActualizar) . " WHERE Id_Usuario = '$user_id'";

        $EXECactualizarRegistroUsuario = mysqli_query($conn, $consultaActualizar);

        if ($EXECactualizarRegistroUsuario) {
            echo "Registro actualizado correctamente.";
            header("Location: perfil.php"); // Redirigir al perfil u otra página de confirmación
            exit();
        } else {
            echo "<script>alert('Error al actualizar el registro.');</script>";
        }
    } else {
        echo "<script>alert('No se han realizado cambios.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/styles.css" rel="stylesheet">

</head>
<body>

 <!-- Nav bar -->
 <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <!-- Barra de búsqueda -->
            <form class="d-flex search-bar ms-4 me-auto">
                <input class="form-control me-2" type="search" placeholder="Buscar productos..." aria-label="Buscar">
                <a class="btn btn-warning" href="ResultadoBusqueda.php">Buscar</a>
            </form>

            <!-- Iconos de cuenta y carrito -->
            <ul class="navbar-nav">
                <!-- Cuenta y listas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://img.icons8.com/ios-filled/50/ffffff/user.png" alt="Cuenta" width="20"> Cuenta
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="perfil.php">Mi cuenta</a></li>
                        <li><a class="dropdown-item" href="Editarperfil.php">Editar Cuenta</a></li>
                        <li><a class="dropdown-item" href="ChatCotizacion.php">Mensajes Privados</a></li>                                                

                        <?php if ($rol_usuario === 'Vendedor'): ?>
                            <li><a class="dropdown-item" href="PublicarProducto.php">Publicar Producto</a></li>
                            <li><a class="dropdown-item" href="ConsultaVentas.php">Consultar Ventas</a></li>
                            <li><a class="dropdown-item" href="ConsultaProductos.php">Ver Mis Productos</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="HistorialDeCompras.php">Historial de Compras</a></li>
                            <li><a class="dropdown-item" href="listas.php">Mis listas</a></li>
                        <?php endif; ?>

                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#crearcategoria">Crear Categoria</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="CerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>

                <!-- Carrito de compras -->
                <li class="nav-item">
                <a class="nav-link" href="CarritoCompra.php">
                <img src="https://img.icons8.com/ios-filled/50/ffffff/shopping-cart.png" alt="Carrito" width="20"> Carrito
                    </a>
                </li>
            </ul>
        </div>
    </nav>

   


      <!-- PARA CREAR UNA CATEGORIA SE ABRE ESTA VENTANA-->
      <div class="modal fade" id="crearcategoria" tabindex="-1" aria-labelledby="crearcategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="color:black">
            <div class="modal-header">
                <h5 class="modal-title" id="crearcategoriaLabel">Crear Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCategoriaForm">
                    <div class="mb-3">
                        <label for="NameCategoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="NameCategoria" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoriadescripcion" class="form-label">Descripción de Categoría</label>
                        <textarea class="form-control" id="categoriadescripcion" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="crearCategoria()">Crear Categoría</button>
            </div>
        </div>
    </div>
</div>

    <!-- Editar Usuario -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-md-6"> 
            <div class="card bg-dark text-white p-4">
                <h2 class="text-center mb-4">Editar Perfil</h2>
                <form id="formulario" action="#" method="post" enctype="multipart/form-data">
                <input type="hidden" id="usuarioIdActual" value="<?php echo $user_id; ?>" />

                    
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
                        <p id="currentImageName" class="text"></p> 


                        <div id="ContainerAvatarRegistro" class="mt-3">
                            <img id="imagePreview" src="#" alt="Vista previa de la imagen" style="display: none;">
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

                    <!-- Botón de Confirmacion -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-warning"  name="GuardarCambios">Guardar Cambios</button>
                    </div>

                    <div class="d-grid mb-3">
                        <!-- Botón de Baja -->
                        <button type="button" class="btn btn-danger mt-3" id="botonBaja" onclick="confirmarBaja()">Darse de Baja</button>
                    </div>        
                    
                </form>
            </div>
        </div>
    </div>

    <script>
    function confirmarBaja() {
    if (confirm("¿Estás seguro de que deseas darte de baja? Esta acción cambiará tu estatus a inactivo.")) {
        // Crear un formulario oculto para enviar la solicitud de baja
        const formularioBaja = document.createElement("form");
        formularioBaja.method = "POST";
        formularioBaja.action = "";

        // Añadir un input hidden con la acción de baja
        const inputAccion = document.createElement("input");
        inputAccion.type = "hidden";
        inputAccion.name = "accion";
        inputAccion.value = "baja";

        formularioBaja.appendChild(inputAccion);

        // Añadir el formulario al documento y enviarlo
        document.body.appendChild(formularioBaja);
        formularioBaja.submit();
    }
    }
    </script>                        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!--PARA VALIDACIONES -->
    <script src ="Editarperfil.js"></script> 
</body>
</html>
