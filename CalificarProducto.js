
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




//Validaciones De Comentarios  
document.querySelectorAll(".btn-gusto").forEach((button) => {
    button.addEventListener("click", function () {
        const group = this.dataset.group;
        const parent = this.parentNode;

        parent.querySelectorAll(`[data-group="${group}"]`).forEach((btn) => {
            btn.classList.remove("active");
        });

        this.classList.add("active");

        const hiddenInput = parent.querySelector(".gusto-seleccionado");
        hiddenInput.value = this.dataset.value;
    });
});


// Validar campos al publicar comentarios
document.getElementById("publicarComentarios").addEventListener("click", function (e) {
    e.preventDefault(); 

    const productos = document.querySelectorAll(".producto");
    let valid = true;

    productos.forEach((producto) => {
        const reseña = producto.querySelector(`#reseñaProducto${producto.dataset.idTransaccion}`);
        const gustoSeleccionado = producto.querySelector(`#gustoSeleccionado${producto.dataset.idTransaccion}`);
    
        if (reseña && !reseña.value.trim()) {
            alert(`Por favor, llena la reseña para el producto ${producto.dataset.idTransaccion}`);
            valid = false;
            return;
        }
    
        if (gustoSeleccionado && !gustoSeleccionado.value) {
            alert(`Por favor, selecciona si te gustó o no el producto ${producto.dataset.idTransaccion}`);
            valid = false;
            return;
        }
    });
    
    

    if (valid) {
        // Si todas las validaciones son correctas, proceder con el envío de datos
        alert("¡Comentarios enviados correctamente!");

        // Recoger los productos y sus datos
        const productosData = Array.from(productos).map(producto => {
            const idTransaccion = producto.getAttribute('data-id-transaccion');
            const reseña = producto.querySelector('.reseña-producto').value;
            const gusto = producto.querySelector('.gusto-seleccionado').value;

            return { idTransaccion, reseña, gusto };
        });

        // Enviar los datos al servidor
        fetch('PublicarComentarios.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productos: productosData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Comentarios publicados con éxito.');

                // Hacer la petición 
                fetch('eliminar_productos_sesionCOMENTARIOS.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'eliminar_productos' })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        window.location.href = 'home.php';
                    } else {
                        console.error('Error al eliminar productos:', result.message);
                        alert(result.message);
                    }
                })
            } else {
                alert('Error al publicar comentarios: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un problema al publicar los comentarios.');
        });
    }
});
