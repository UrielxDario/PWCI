function cambiarImagen(index) {
    var carrusel = document.getElementById('productoCarrusel');
    var bsCarrusel = new bootstrap.Carousel(carrusel);
    bsCarrusel.to(index);
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
document.getElementById('cantidad').addEventListener('keydown', function(event) {
    if (event.key !== "ArrowUp" && event.key !== "ArrowDown") {
        event.preventDefault();
    }
});



//Para obtener las listas y abrir el modal
document.getElementById('abrirModalListasBtn').addEventListener('click', function() {
    fetch('obtenerlistas.php', {
        method: 'GET'
    })
    .then(response => response.json())
    .then(data => {
        const listaSelect = document.getElementById('listaSeleccionada');
        listaSelect.innerHTML = '';  
        data.forEach(lista => {
            const option = document.createElement('option');
            option.value = lista.ID_LISTA; 
            option.textContent = lista.NombreLista; 
            option.setAttribute('data-nombre', lista.NombreLista); 
            listaSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error al obtener las listas:', error);
    });

    const modal = new bootstrap.Modal(document.getElementById('modalListas'));
    modal.show();
});


document.getElementById('confirmarAgregarListaBtn').addEventListener('click', function() {
    const listaSeleccionada = document.getElementById('listaSeleccionada');
    const listaNombre = listaSeleccionada.options[listaSeleccionada.selectedIndex].getAttribute('data-nombre');
    const idLista = listaSeleccionada.value;  
    const idProducto = document.getElementById('producto').getAttribute('data-id');  

    if (!idLista) {
        alert('Por favor, selecciona una lista.');
        return;
    }

    fetch('AgregarProductoLista.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id_lista=${idLista}&id_producto=${idProducto}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Producto agregado a la lista: ' + listaNombre);
        } else {
            alert('Hubo un error al agregar el producto.');
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('modalListas'));
        modal.hide();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al agregar el producto a la lista.');
    });
});
