<?php
header('Content-Type: application/json');
require 'conexionBaseDeDatos.php'; // Asegúrate de tener este archivo correctamente configurado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lee los datos de la solicitud
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $orden = $data['orden'] ?? ''; // Orden de los productos (e.g., precio ascendente/descendente)
    $categoria = $data['categoria'] ?? ''; // ID o nombre de la categoría

    $query = "SELECT p.ID_PRODUCTO, p.NombreProducto, p.DescripcionProducto, p.PrecioProducto, m.Archivo AS imagen 
              FROM producto p 
              LEFT JOIN multimedia m ON p.ID_PRODUCTO = m.ID_PRODUCTO"; // Unión con multimedia para obtener la imagen

    $conditions = [];
    $params = [];

    // Filtros dinámicos
    if (!empty($categoria)) {
        $conditions[] = "p.ID_CATEGORIA = ?";
        $params[] = $categoria;
    }

    // Aplica orden si existe
    if (!empty($orden)) {
        if ($orden === 'precio_asc') {
            $query .= " ORDER BY p.PrecioProducto ASC";
        } elseif ($orden === 'precio_desc') {
            $query .= " ORDER BY p.PrecioProducto DESC";
        } else {
            $query .= " ORDER BY p.NombreProducto ASC"; // Orden predeterminado
        }
    }

    // Añade las condiciones al query si existen
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    try {
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = [
                'ID_PRODUCTO' => $row['ID_PRODUCTO'],
                'NombreProducto' => $row['NombreProducto'],
                'DescripcionProducto' => $row['DescripcionProducto'],
                'PrecioProducto' => $row['PrecioProducto'],
                'imagen' => $row['imagen'] ?? 'img/user.jpg' // Imagen por defecto si no hay una asociada
            ];
        }

        echo json_encode($productos);

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>
