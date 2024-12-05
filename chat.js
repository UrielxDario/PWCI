document.querySelectorAll('.agregarCarritoBtn').forEach(function(button) {
    button.addEventListener('click', function() {
        // Obtener el ID del producto y la cantidad
        const idProducto = this.getAttribute('data-id');
        const cantidad = parseInt(document.getElementById('cantidad_' + idProducto).value);

        if (cantidad <= 0) {
            alert("Por favor, selecciona una cantidad válida.");
            return;
        }

        // Enviar la solicitud para agregar al carrito
        fetch('AgregarProductoACarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_producto: idProducto, cantidad: cantidad })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Producto agregado al carrito exitosamente.");
            } else {
                alert("Error al agregar producto al carrito: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            alert("Ocurrió un error al agregar el producto.");
        });
    });
});
