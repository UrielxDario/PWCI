const productList = document.getElementById('productList');
    

    
    const listas = {
        'Lista de Zapatos': [
            { name: 'Tenis A', description: 'Descripción del Tenis A', price: '59.99' },
            { name: 'Tenis B', description: 'Descripción del Tenis B', price: '49.99' }
        ],
        'Lista de Ropa': [
            { name: 'Camiseta X', description: 'Descripción de la Camiseta X', price: '19.99' },
            { name: 'Pantalón Y', description: 'Descripción del Pantalón Y', price: '39.99' }
        ]
    };

    
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', () => {
            const listaNombre = item.querySelector('strong').innerText;
            mostrarProductos(listaNombre);
        });
    });

    function mostrarProductos(listaNombre) {
        productList.innerHTML = ''; 
        const productos = listas[listaNombre];

        productos.forEach(producto => {
            const card = document.createElement('div');
            card.className = 'card mb-3';
            card.innerHTML = `
                <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">${producto.name}</h5>
                    <p class="card-text">${producto.description}</p>
                    <p class="card-text"><strong>Precio: $${producto.price}</strong></p>
                    
                </div>
                    <button class="btn btn-danger", '${listaNombre}')">Eliminar Producto</button>
                </div>
                
            `;
            productList.appendChild(card);
        });
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
    