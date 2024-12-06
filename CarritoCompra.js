function mostrarInputNumero() {
    const metodoPago = document.getElementById('metodoPago').value;
    const inputNumeroContainer = document.getElementById('inputNumeroContainer');
    
    if (metodoPago === 'paypal' || metodoPago === 'tarjeta') {
        inputNumeroContainer.style.display = 'block';
    } else {
        inputNumeroContainer.style.display = 'none';
    }
}



function crearCategoria() {
    const nombreCategoria = document.getElementById('NameCategoria').value;
    const descripcionCategoria = document.getElementById('categoriadescripcion').value;

    fetch('CrearCategoria.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombreCategoria=${encodeURIComponent(nombreCategoria)}&descripcionCategoria=${encodeURIComponent(descripcionCategoria)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Categoría creada exitosamente');

            const modal = bootstrap.Modal.getInstance(document.getElementById('crearcategoria'));
            modal.hide();

            document.getElementById('createCategoriaForm').reset();

        } else {
            alert('Hubo un problema al crear la categoría');
        }
    })
    .catch(error => console.error('Error:', error));
}

//Para que no se pueda escribir con el teclado la cantidad
document.addEventListener('DOMContentLoaded', () => {
    const cantidadInputs = document.querySelectorAll('.cantidad-producto');

    cantidadInputs.forEach(input => {
        input.addEventListener('keydown', function(event) {
            if (event.key !== "ArrowUp" && event.key !== "ArrowDown") {
                event.preventDefault();
            }
        });
    });
});





//Pa actualizar la cantidad constantemente 
document.addEventListener('DOMContentLoaded', () => {
    const cantidades = document.querySelectorAll('.cantidad-producto');
    const totalSpan = document.getElementById('total');

    // pa calcular el total 
    const calcularTotal = () => {
        let total = 0;
        cantidades.forEach(input => {
            const precio = parseFloat(input.dataset.precio);
            const cantidad = parseInt(input.value);
            total += precio * cantidad;
        });
        totalSpan.textContent = `$${total.toFixed(2)}`;
    };

    // Si hay cambios en las cantidades
    cantidades.forEach(input => {
        input.addEventListener('input', (e) => {
            const cantidad = parseInt(e.target.value);
            const maxCantidad = parseInt(input.max);

            if (cantidad >= 1 && cantidad <= maxCantidad) {
                calcularTotal();
                actualizarCantidad(input, cantidad);
            } else {
                alert(`La cantidad debe ser entre 1 y ${maxCantidad}`);
                input.value = Math.max(1, Math.min(cantidad, maxCantidad)); 
            }
        });
    });

    // AJAX para actualizar la cantidad en la base de datos
    const actualizarCantidad = (input, cantidad) => {
        const productoId = input.closest('.producto').querySelector('.eliminar-producto').dataset.id;
        fetch('actualizar_cantidadcarrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_producto: productoId, cantidad })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                calcularTotal();
            } else {
                alert('Error al actualizar la cantidad en la base de datos.');
            }
        });
    };

    calcularTotal();
});




// Función para eliminar el producto del carrito
document.querySelectorAll('.eliminar-producto').forEach(button => {
    button.addEventListener('click', function() {
        let idProducto = this.getAttribute('data-id');  
        let idCarrito = this.getAttribute('data-carrito');  
        let idUsuario = this.getAttribute('data-usuario');  

        console.log(`id_producto: ${idProducto}, id_carrito: ${idCarrito}, id_usuario: ${idUsuario}`);  

        if (confirm("¿Estás seguro de que deseas eliminar este producto de tu carrito?")) {
            fetch('EliminarProductoCarrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_producto=${idProducto}&id_carrito=${idCarrito}&id_usuario=${idUsuario}`
            })
            .then(response => response.text())
            .then(text => {
                console.log('Respuesta del servidor:', text);
                try {
                    const data = JSON.parse(text);  
                    if (data.success) {
                        let productoElement = button.closest('.producto');
                        productoElement.remove();

                        if (data.total !== null) {
                            document.getElementById('total').textContent = `$${data.total.toFixed(2)}`;
                        } else {
                            document.getElementById('total').textContent = '$0.00';
                        }
                    } else {
                        alert('Error al eliminar el producto.');
                    }
                } catch (error) {
                    console.error('Error al parsear JSON:', error);
                }
            })
            .catch(error => console.error('Error al eliminar producto:', error));
        }
    });
});



//Para cuando se confirme la compra se mandan los datos en un JSON
document.getElementById('confirmarCompra').addEventListener('click', function () {
   

    const productos = [];
document.querySelectorAll('.cantidad-producto').forEach(input => {
    productos.push({
        ID_PRODUCTO: input.dataset.id, 
        cantidad: parseInt(input.value, 10), 
        PrecioProducto: parseFloat(input.dataset.precio),
        NombreProducto: input.closest('.producto').querySelector('.nombre-producto').textContent.trim(), // .nombre-producto
        ImgArchivo: input.closest('.producto').querySelector('.imagen-producto').src, // .imagen-producto
        ID_CARRITO: input.closest('.producto').querySelector('.eliminar-producto').dataset.carrito
    });
});


    const datos = {
        productos
    };

    fetch('ConfirmarCompra.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos),
    })
    .then(response => response.text())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.status === 'success') {
                alert('Compra realizada con éxito.');
                window.location.href = 'CalificarProducto.php';
            } else {
                alert('Error al realizar la compra: ' + jsonData.message);
            }
        } catch (e) {
            console.error('Error al analizar la respuesta:', e);
            alert('Error al procesar la respuesta del servidor.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error. Inténtalo nuevamente.');
    });
});

