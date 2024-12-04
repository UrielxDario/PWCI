<?php
include 'conexionBaseDeDatos.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$rol_usuario = $_SESSION['rol_usuario'];
$id_usuario = $_SESSION['id_usuario']; // El ID del usuario (cliente o vendedor)

// Manejar la creación o redirección de chats basados en id_usuario
if (isset($_GET['id_vendedor'])) {
    $id_otro_usuario = $_GET['id_vendedor'];
    
    // Determinar roles según el usuario actual
    if ($rol_usuario === 'Cliente') {
        $query_chat = "SELECT ID_Chat FROM Chat WHERE ID_Cliente = ? AND ID_Vendedor = ?";
        $stmt_chat = $conn->prepare($query_chat);
        $stmt_chat->bind_param('ii', $id_usuario, $id_otro_usuario);
    } else if ($rol_usuario === 'Vendedor') {
        $query_chat = "SELECT ID_Chat FROM Chat WHERE ID_Vendedor = ? AND ID_Cliente = ?";
        $stmt_chat = $conn->prepare($query_chat);
        $stmt_chat->bind_param('ii', $id_usuario, $id_otro_usuario);
    }

    $stmt_chat->execute();
    $result_chat = $stmt_chat->get_result();

    if ($result_chat->num_rows > 0) {
        // El chat ya existe, redirigir al ID del chat
        $chat_existente = $result_chat->fetch_assoc();
        header("Location: chat.php?id_chat=" . $chat_existente['ID_Chat']);
        exit();
    } else {
        // El chat no existe, crearlo
        if ($rol_usuario === 'Cliente') {
            $query_insert_chat = "INSERT INTO Chat (ID_Cliente, ID_Vendedor, FechaInicio) VALUES (?, ?, NOW())";
        } else if ($rol_usuario === 'Vendedor') {
            $query_insert_chat = "INSERT INTO Chat (ID_Vendedor, ID_Cliente, FechaInicio) VALUES (?, ?, NOW())";
        }

        $stmt_insert_chat = $conn->prepare($query_insert_chat);
        $stmt_insert_chat->bind_param('ii', $id_usuario, $id_otro_usuario);
        $stmt_insert_chat->execute();

        // Obtener el ID del chat recién creado
        $nuevo_id_chat = $conn->insert_id;
        header("Location: chat.php?id_chat=" . $nuevo_id_chat);
        exit();
    }
}
// Si el usuario es cliente, muestra los chats con los vendedores
if ($rol_usuario === 'Cliente') {
    $query = "SELECT c.ID_Chat, u.Username AS Vendedor
              FROM Chat c
              JOIN Usuario u ON c.ID_Vendedor = u.ID_USUARIO
              WHERE c.ID_Cliente = ? 
              ORDER BY c.FechaInicio DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
} else if ($rol_usuario === 'Vendedor') {
    // Si el usuario es vendedor, muestra los chats con los clientes
    $query = "SELECT c.ID_Chat, u.Username AS Cliente
              FROM Chat c
              JOIN Usuario u ON c.ID_Cliente = u.ID_USUARIO
              WHERE c.ID_Vendedor = ? 
              ORDER BY c.FechaInicio DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Obtener los mensajes de un chat específico
if (isset($_GET['id_chat'])) {
    $id_chat = $_GET['id_chat'];
    $query_mensajes = "SELECT m.ID_Mensaje, m.TextoMensaje, m.HoraFechaMensaje, u.Username AS Usuario, u.Rol AS Rol, u.ImgPerfil AS Imagen
                       FROM Mensaje m
                       JOIN Usuario u ON m.ID_USUARIO = u.ID_USUARIO
                       WHERE m.CHAT_ID = ? 
                       ORDER BY m.HoraFechaMensaje ASC";
    $stmt_mensajes = $conn->prepare($query_mensajes);
    $stmt_mensajes->bind_param('i', $id_chat);
    $stmt_mensajes->execute();
    $mensajes = $stmt_mensajes->get_result();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    // Enviar un mensaje nuevo
    $mensaje = $_POST['mensaje'];
    $chat_id = $_POST['chat_id'];
    $id_usuario = $_SESSION['id_usuario'];
    $query_insertar = "INSERT INTO Mensaje (CHAT_ID, ID_USUARIO, TextoMensaje, HoraFechaMensaje) 
                       VALUES (?, ?, ?, NOW())";
    $stmt_insertar = $conn->prepare($query_insertar);
    $stmt_insertar->bind_param('iis', $chat_id, $id_usuario, $mensaje);
    $stmt_insertar->execute();
    header("Location: chat.php?id_chat=" . $chat_id); // Redirigir al mismo chat para ver el nuevo mensaje
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="estilos/ChatCotizacion.css" rel="stylesheet"> 
</head>
<body>
    <!-- Nav bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="home.php">The Dark Wardrobe</a>
                <form class="d-flex search-bar ms-4 me-auto">
                    <input class="form-control me-2" type="search" placeholder="Buscar productos..." aria-label="Buscar">
                    <a class="btn btn-warning" href="ResultadoBusqueda.php">Buscar</a>
                </form>
                <ul class="navbar-nav">
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
                    <li class="nav-item">
                        <a class="nav-link" href="CarritoCompra.php">
                            <img src="https://img.icons8.com/ios-filled/50/ffffff/shopping-cart.png" alt="Carrito" width="20"> Carrito
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
<div>
        <div class="container my-4">
            <h2 id="titulo-chat">Mensajes Privados - Cotizaciones de Productos</h2>
            <p>Conversación entre Cliente y Vendedor</p>
        </div>

    <div class="container">
        <div class="row">
            <!-- Lista de Vendedores o clientes  -->
            <div class="col-md-3">
                <h4><?php echo ($rol_usuario === 'Vendedor') ? 'Clientes / Cotizaciones' : 'Vendedores / Cotizaciones'; ?></h4>
                <ul class="list-group">
                    <?php while ($chat = $result->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <a href="chat.php?id_chat=<?= $chat['ID_Chat']; ?>" class="vendedor-enlace"><?= $rol_usuario === 'Vendedor' ? $chat['Cliente'] : $chat['Vendedor']; ?> </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Mensajes -->
            <div class="col-md-9">
                <?php if (isset($mensajes)): ?>
                    <div class="contenedor-mensajes">
                        <?php if ($mensajes->num_rows > 0): ?>
                            <?php while ($mensaje = $mensajes->fetch_assoc()): ?>
                                <div class="mensaje <?= ($mensaje['Rol'] == 'Cliente') ? 'cliente' : 'vendedor'; ?>">
                                    <img src="img/<?= htmlspecialchars($mensaje['Imagen']); ?>" alt="<?= htmlspecialchars($mensaje['Usuario']); ?>">
                                    <div class="contenido-mensaje">
                                        <p><?= htmlspecialchars($mensaje['TextoMensaje']); ?></p>
                                        <span class="marca-tiempo"><?= htmlspecialchars($mensaje['HoraFechaMensaje']); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                Aún no hay mensajes en esta conversación. ¡Escribe el primero!
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Enviar Nuevo Mensaje -->
                    <div class="enviar-mensaje">
                        <form action="chat.php" method="POST">
                            <textarea class="form-control" rows="3" placeholder="Escribe tu mensaje aquí..." name="mensaje"></textarea>
                            <input type="hidden" name="chat_id" value="<?= $id_chat; ?>">
                            <button class="btn btn-enviar" type="submit">Enviar</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
