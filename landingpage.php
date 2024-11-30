
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Dark Wardrobe - Landing Page</title>
    <!-- Enlace a Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/styles.css" rel="stylesheet">

</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="landingpage.php">The Dark Wardrobe</a>
            
            

           
        </div>
    </nav>


    <!-- Sección principal de bienvenida -->
    <section class="hero-section" id="landingpagefondo" style="height: 80vh;">
    <video autoplay loop muted playsinline>
        <source src="img/CapaIntermediaTiendaRopa.mp4" type="video/mp4">
    </video>    <div class="hero-text">
            <h1 class="display-4">¡Bienvenido a The Dark Wardrobe!</h1>
            <p class="lead">Donde encontrarás las mejores prendas para crear un estilo tan único como tu</p>
            <p class="lead">Regístrate o inicia sesión para disfrutar de una experiencia de compra única</p>
            <a href="login.php" class="btn btn-warning btn-lg me-2">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-secondary btn-lg">Registrarse</a>
        </div>
    </section>

    <!-- Productos mejor calificados -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Nuestros Productos Mejor Calificados</h2>
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Producto 1 -->
                <div class="carousel-item active">
                    <div class="d-flex justify-content-center">
                        <img src="img/Crocs.jpg" class="d-block w-50" alt="Producto 1">
                    </div>
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Producto 1</h5>
                        <p>Descripción del producto 1.</p>
                    </div>
                </div>
                <!-- Producto 2 -->
                <div class="carousel-item">
                    <div class="d-flex justify-content-center">
                        <img src="img/Cherc.jpg" class="d-block w-50" alt="Producto 2">
                    </div>
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Producto 2</h5>
                        <p>Descripción del producto 2.</p>
                    </div>
                </div>
                <!-- Producto 3 -->
                <div class="carousel-item">
                    <div class="d-flex justify-content-center">
                        <img src="img/JordanMiles.jpg" class="d-block w-50" alt="Producto 3">
                    </div>
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Producto 3</h5>
                        <p>Descripción del producto 3.</p>
                    </div>
                </div>
            </div>
            <!-- Controles del carrusel -->
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>


    <!-- Productos mas vendidos -->    
    <div class="container my-5">
        <h2 class="text-center mb-4">Productos más vendidos</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="img/Kimono.jpg" class="card-img-top" alt="Producto 1">
                    <div class="card-body">
                        <h5 class="card-title">Producto 1</h5>
                        <p class="card-text">Descripción breve del producto.</p>
                        <a href="#" class="btn btn-warning">Comprar ahora</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="img/SombreroRDR.jpg" class="card-img-top" alt="Producto 2">
                    <div class="card-body">
                        <h5 class="card-title">Producto 2</h5>
                        <p class="card-text">Descripción breve del producto.</p>
                        <a href="#" class="btn btn-warning">Comprar ahora</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="img/Akira.jpg" class="card-img-top" alt="Producto 3">
                    <div class="card-body">
                        <h5 class="card-title">Producto 3</h5>
                        <p class="card-text">Descripción breve del producto.</p>
                        <a href="#" class="btn btn-warning">Comprar ahora</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <br><br>

    <footer class="bg-dark text-light text-center py-3">
        <p>&copy; 2024 The Dark Wardrobe - Todos los derechos reservados - Creado por Uriel Arguello y Luis Carrizales</p>
    </footer>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
