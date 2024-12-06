<?php

require 'conexionBaseDeDatos.php';
// Consulta para productos mejor calificados
$sqlMejorCalificados = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, m.Archivo 
                        FROM Producto p
                        JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
                        WHERE p.AutorizacionAdmin = 'Si'
                        GROUP BY p.ID_PRODUCTO
                        ORDER BY p.PromedioCalificacion DESC
                        LIMIT 3;";
$resultMejorCalificados = $conn->query($sqlMejorCalificados);

// Consulta para productos más vendidos
$sqlMasVendidos = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, m.Archivo, SUM(t.CantidadComprada) AS TotalVentas
                    FROM Producto p
                    JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO
                    JOIN Transacción t ON p.ID_PRODUCTO = t.ID_PRODUCTO
                    WHERE p.AutorizacionAdmin = 'Si'
                    GROUP BY p.ID_PRODUCTO
                    ORDER BY TotalVentas DESC
                    LIMIT 3;";
$resultMasVendidos = $conn->query($sqlMasVendidos);

?>
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
        <h2 class="text-center mb-4">Productos Mejor Calificados</h2>
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
                $active = true;
                while ($producto = $resultMejorCalificados->fetch_assoc()) {
                    $imgData = base64_encode($producto['Archivo']);
                    $activeClass = $active ? 'active' : '';
                    $active = false;
                    echo "
                    <div class='carousel-item $activeClass'>
                        <div class='d-flex justify-content-center'>
                            <img src='data:image/jpeg;base64,$imgData' class='d-block w-50' alt='{$producto['NombreProducto']}'>
                        </div>
                        <div class='carousel-caption d-none d-md-block text-warning fw-bold'>
                            <h5>{$producto['NombreProducto']}</h5>
                            <p>{$producto['DescripcionProducto']}</p>
                        </div>
                    </div>";
                }
            ?>
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
        <h2 class="text-center mb-4">Productos Más Vendidos</h2>
        <div class="row">
        <?php
            while ($producto = $resultMasVendidos->fetch_assoc()) {
                $imgData = base64_encode($producto['Archivo']);
                echo "
                <div class='col-md-4'>
                    <div class='card'>
                        <img src='data:image/jpeg;base64,$imgData' class='card-img-top' alt='{$producto['NombreProducto']}'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$producto['NombreProducto']}</h5>
                            <p class='card-text'>{$producto['DescripcionProducto']}</p>
                            <a href='#' class='btn btn-warning'>Comprar ahora</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
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
