<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/MiPerfilAdmin.css" rel="stylesheet">
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
                        <li><a class="dropdown-item" href="MiPerfilAdmin.php">Mi cuenta</a></li>
                        <li><a class="dropdown-item" href="AutorizarProductos.php">Autorizar Productos</a></li>
                       
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="CerrarSesion.php">Cerrar sesión</a></li>
                    </ul>
                </li>

                
            </ul>
        </div>
    </nav>



    <div class="container mt-5">
        <h1 class="text-center">Perfil de Usuario</h1>
        <div class="card">
            <div class="card-body text-center">
                <img src="img/user.jpg" alt="Foto de Perfil" class="rounded-circle" width="150">
                <h2>Nombre de Usuario</h2>
                <p class="text-muted">Administrador</p>
              
            </div>
        </div>

       
      
        <!-- Si es Admin -->
        <div class="mt-4">
            <h3>Productos Autorizados</h3>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">Producto X</h5>
                    <p>Descripción del Producto X - <strong>Precio: $29.99</strong></p>
                    <p>Vendedor: Juanito</p> 
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title">Producto Y</h5>
                    <p>Descripción del Producto Y - <strong>Precio: $99.99</strong></p>
                    <p>Vendedor: Pedrito</p> 
                </div>
            </div>
        </div>

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
