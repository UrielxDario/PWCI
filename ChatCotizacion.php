<?php
include 'conexionBaseDeDatos.php';
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario']; // El ID del usuario logueado

// Obtener el rol del usuario
$query_rol = "SELECT Rol FROM Usuario WHERE ID_USUARIO = ?";
$stmt_rol = $conn->prepare($query_rol);
$stmt_rol->bind_param('i', $id_usuario);
$stmt_rol->execute();
$result_rol = $stmt_rol->get_result();
$rol_usuario = $result_rol->fetch_assoc()['Rol'];

// Consulta para obtener los chats dependiendo del rol
if ($rol_usuario === 'Cliente') {
    // Si el usuario es cliente, obtener chats donde él sea el cliente
    $query = "SELECT c.ID_Chat, u.Username AS Vendedor
              FROM Chat c
              JOIN Usuario u ON c.ID_Vendedor = u.ID_USUARIO
              WHERE c.ID_Cliente = ? 
              ORDER BY c.FechaInicio DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_usuario);
} else {
    // Si el usuario es vendedor, obtener chats donde él sea el vendedor
    $query = "SELECT c.ID_Chat, u.Username AS Cliente
              FROM Chat c
              JOIN Usuario u ON c.ID_Cliente = u.ID_USUARIO
              WHERE c.ID_Vendedor = ? 
              ORDER BY c.FechaInicio DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_usuario);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver y crear Chats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ChatCotizacion.css" rel="stylesheet">
</head>

<body>
     <!-- Nav bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
            
            <!-- Barra de búsqueda xd -->
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

<div class="container mt-4">
    <h3>Mis Chats</h3>
    
    <div class="list-group">
            <?php while ($chat = $result->fetch_assoc()): ?>
                <a href="chat.php?id_chat=<?= $chat['ID_Chat'] ?>" class="list-group-item list-group-item-action">
                    Chat con: <?= htmlspecialchars($chat[$rol_usuario === 'Cliente' ? 'Vendedor' : 'Cliente']) ?>
                </a>
            <?php endwhile; ?>
    </div>

    <hr>
    <h5>Iniciar un nuevo chat</h5>
    <form action="inicioChat.php" method="GET">
        <div class="mb-3">
            <label for="id_vendedor" class="form-label">Selecciona un vendedor</label>
            <select class="form-select" name="id_vendedor" required>
                <?php
                // Lista de vendedores disponibles para iniciar chat
                $query_vendedores = "SELECT ID_USUARIO, Username FROM Usuario WHERE Rol = 'Vendedor'";
                $result_vendedores = $conn->query($query_vendedores);
                while ($vendedor = $result_vendedores->fetch_assoc()):
                ?>
                    <option value="<?= $vendedor['ID_USUARIO'] ?>"><?= htmlspecialchars($vendedor['Username']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-warning">Iniciar Chat</button>
    </form>
</div>

    <!-- Enlace a Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="ChatCotizacion.js"></script>

</body>
</html>
