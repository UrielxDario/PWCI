// Obtener el ID del usuario desde la URL (si existe)
const urlParams = new URLSearchParams(window.location.search);
const userIdFromUrl = urlParams.get('id_usuario');

// Usamos el ID del usuario que se pasó por URL, si existe; de lo contrario, usamos el usuario que está en sesión.
const urlToFetch = userIdFromUrl ? `api/get_user_data.php?id_usuario=${userIdFromUrl}` : 'api/get_user_data.php';

fetch(urlToFetch)
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
    // Lógica para cargar listas públicas desde la base de datos (ejemplo modificado)
    fetch(`api/get_user_lists.php?username=${username}`)
        .then(response => response.json())
        .then(lists => {
            let listsHTML = `<h3>Listas Públicas</h3>`;
            lists.forEach(list => {
                listsHTML += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">${list.NombreLista}</h5>
                            <p>${list.DescripcionLista}</p>
                            <strong>Privacidad:</strong> ${list.PrivacidadLista}

                            <h6>Productos en la lista:</h6>
                    <div class="row">
                `;
                list.productos.forEach(product => {
                    listsHTML += `
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <img src="${product.ImgProducto}" class="card-img-top" alt="${product.NombreProducto}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.NombreProducto}</h5>
                                    <p class="card-text">Precio: $${product.PrecioProducto}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                listsHTML += `</div></div></div>`;
            });
            document.getElementById('userLists').innerHTML = listsHTML;
        })
        .catch(error => console.error('Error al cargar listas:', error));
}

function loadPublishedProducts(username) {
    // Lógica para cargar productos publicados (ejemplo modificado)
    fetch(`api/get_user_products.php?username=${username}`)
        .then(response => response.json())
        .then(products => {
            let productsHTML = `<h3>Productos Publicados</h3>`;
            if (products.length === 0) {
                productsHTML += `<p>No hay productos publicados autorizados.</p>`;
            } else {
            products.forEach(product => {
                productsHTML += `
                    <div class="card mb-3" style="max-width: 540px;">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="${product.ImgProducto}" class="img-fluid rounded-start" alt="${product.NombreProducto}">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">${product.NombreProducto}</h5>
                                    <p class="card-text"><strong>Precio:</strong> $${product.PrecioProducto}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            }
            document.getElementById('userProducts').innerHTML = productsHTML;
        })
        .catch(error => console.error('Error al cargar productos:', error));
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
