fetch('api/get_user_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                } else {
                    // Asignación de valores a los campos
                    document.getElementById('profileImage').src = 'img/' + data.ImgPerfil; 
                    document.getElementById('username').textContent = data.Username;
                    
                    // Mostrar el estado de privacidad
                    if (data.PrivacidadUsuario === 'privado') {
                        document.getElementById('privacyStatus').textContent = 'Perfil Privado';
                        document.getElementById('userLists').innerHTML = '<h3>Estado del Perfil</h3><p>Este perfil es Privado</p>';
                        document.getElementById('userProducts').innerHTML = '';
                    } else {
                        document.getElementById('privacyStatus').textContent = 'Perfil Público';
                        
                        // Verifica el rol del usuario y carga listas o productos
                        if (data.Rol === 'Vendedor') {
                            loadPublishedProducts(data.Username);
                        } else {
                            loadPublicLists(data.Username);
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error al obtener datos del usuario:', error);
            });

function loadPublicLists(username) {
    // Lógica para cargar listas públicas
    // Este es un ejemplo, reemplaza con tu lógica para obtener listas
    const listsHTML = `
        <h3>Listas Públicas</h3>
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title">Lista de Zapatos</h5>
                <div class="product">
                    <p><strong>Tenis A</strong>: Descripción A - <strong>Precio: $59.99</strong></p>
                </div>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title">Lista de Ropa</h5>
                <div class="product">
                    <p><strong>Camiseta X</strong>: Descripción de la Camiseta X - <strong>Precio: $19.99</strong></p>
                </div>
            </div>
        </div>
    `;
    document.getElementById('userLists').innerHTML = listsHTML;
}

function loadPublishedProducts(username) {
    // Lógica para cargar productos publicados
    // Este es un ejemplo, reemplaza con tu lógica para obtener productos
    const productsHTML = `
        <h3>Productos Publicados</h3>
        <div class="card mb-2">
            <div class="card-body">
                <h5 class="card-title">Producto X</h5>
                <p>Descripción del Producto X - <strong>Precio: $29.99</strong></p>
            </div>
        </div>
    `;
    document.getElementById('userProducts').innerHTML = productsHTML;
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
