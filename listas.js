


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
    
    function crearLista() {
        const nombreLista = document.getElementById('listName').value.trim();
        const descripcionLista = document.getElementById('listDescription').value.trim();
        const privacidadLista = document.getElementById('isPublic').checked ? 'Pública' : 'Privada';
        
        if (nombreLista === "") {
            alert('El nombre de la lista es obligatorio.');
            return; 
        }
        if (descripcionLista === "") {
            alert('La descripción de la lista es obligatoria.');
            return; 
        }
    
        fetch('CrearLista.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `nombreLista=${encodeURIComponent(nombreLista)}&descripcionLista=${encodeURIComponent(descripcionLista)}&privacidadLista=${encodeURIComponent(privacidadLista)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lista creada exitosamente');
        
                const modal = bootstrap.Modal.getInstance(document.getElementById('createListModal'));
                modal.hide();
                document.getElementById('createListForm').reset();
        
              
                cargarListas();  
            } else {
                alert('Hubo un problema al crear la lista');
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function cargarListas() {
        fetch('obtenerlistas.php')
            .then(response => response.json())
            .then(data => {
                const listGroup = document.querySelector('.list-group');
                listGroup.innerHTML = ''; 
        
                data.forEach(lista => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `
                        <strong>${lista.NombreLista}</strong>
                        <span class="badge bg-${lista.PrivacidadLista === 'Publica' ? 'success' : 'warning'}">
                            ${lista.PrivacidadLista}
                        </span>
                    `;
                    li.addEventListener('click', () => cargarProductos(lista.ID_LISTA));
                    listGroup.appendChild(li);
                });
            })
            .catch(error => console.error('Error al cargar listas:', error));
    }
    
    function cargarProductos(idLista) {
    
        fetch(`obtenerproductos_lista.php?id_lista=${idLista}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
    
                const productList = document.getElementById('productList');
                productList.innerHTML = ''; 
    
                if (data.error) {
                    console.error('Error recibido desde el servidor:', data.error);
                    alert(data.error);
                    return;
                }
    
                if (data.length === 0) {
                    productList.innerHTML = `
                        <p class="alert alert-warning">No se ha agregado ningún producto a esta lista.</p>
                    `;
                    return;
                }
    
                data.forEach(producto => {
    
                    const card = document.createElement('div');
                    card.className = 'card mb-3';
    
                    const precio = producto.PrecioProducto === null ? 'Para Cotización' : `$${producto.PrecioProducto.toFixed(2)}`;
    
                    const botonEliminar = document.createElement('button');
                    botonEliminar.className = 'btn btn-danger btn-sm ms-3';
                    botonEliminar.textContent = 'Borrar Producto de la Lista';
                    botonEliminar.addEventListener('click', () => {
                        eliminarProducto(idLista, producto.ID_ProductoEnLista);
                    });
    
                    card.innerHTML = `
                        <div class="card-body d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">${producto.NombreProducto}</h5>
                                <p class="card-text">${producto.DescripcionProducto}</p>
                                <p class="card-text"><strong>Precio: ${precio}</strong></p>
                            </div>
                            <div></div>
                        </div>
                    `;
    
                    card.querySelector('div:last-child').appendChild(botonEliminar);
    
                    productList.appendChild(card);
                });
            })
            .catch(error => {
                alert('Error al cargar los productos. Revisa la consola para más detalles.');
            });
    }
    
    
    
    // Función para eliminar un producto de la lista
    function eliminarProducto(idLista, idProductoEnLista) {
        if (confirm("¿Estás seguro de que deseas eliminar este producto de la lista?")) {
            fetch('EliminarProductoLista.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_lista=${idLista}&id_producto_en_lista=${idProductoEnLista}`,
            })
            .then(response => response.json())
            .then(data => {

                if (data.success) {
                    alert('Producto eliminado de la lista.');
                    cargarProductos(idLista); 
                } else {
                    alert('Error al eliminar el producto.');
                }
            })
            .catch(error => console.error('Error al eliminar producto:', error));
        }
    }

    
    document.addEventListener('DOMContentLoaded', () => {
        cargarListas();
    });
    


    