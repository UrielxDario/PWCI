function crearCategoria() {
    const nombreCategoria = document.getElementById('NameCategoria').value.trim();
    const descripcionCategoria = document.getElementById('categoriadescripcion').value.trim();

    if (!nombreCategoria || !descripcionCategoria) {
        alert('Por favor completa todos los campos antes de continuar.');
        return;
    }

    fetch('CrearCategoria.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `nombreCategoria=${encodeURIComponent(nombreCategoria)}&descripcionCategoria=${encodeURIComponent(descripcionCategoria)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Categoría creada exitosamente');

            const modal = bootstrap.Modal.getInstance(document.getElementById('crearcategoria'));
            modal.hide();

            document.getElementById('createCategoriaForm').reset();
        } else {
            alert(`Hubo un problema al crear la categoría: ${data.message || 'Error desconocido'}`);
        }
    })
    .catch(error => console.error('Error:', error));
}


/* document.addEventListener("DOMContentLoaded", () => {
    const filtroBusqueda = document.getElementById("filtroBusqueda");
    const filtroCategoria = document.getElementById("filtroCategoria");
    const contenedorResultados = document.querySelector(".container.mt-4");

    if (!filtroBusqueda || !filtroCategoria || !contenedorResultados) {
        console.error("Faltan elementos HTML necesarios para los filtros.");
        return;
    }

    const aplicarFiltros = () => {
        const orden = filtroBusqueda.value.trim();
        const categoria = filtroCategoria.value.trim();

        // Solicitud AJAX
        fetch("filtrarProductos.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ orden, categoria }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener productos.");
            }
            return response.json();
        })
        .then(data => {
            // Limpia los resultados actuales
            contenedorResultados.innerHTML = "";

            if (!Array.isArray(data) || data.length === 0) {
                contenedorResultados.innerHTML = "<p>No se encontraron productos con los filtros seleccionados.</p>";
                return;
            }

            // Recorre los productos y genera el HTML
            data.forEach(producto => {
                const productoHTML = `
                    <a href="VerProducto.php?id=${producto.id}" class="text-decoration-none text-white">
                        <div class="row mb-4 producto" style="cursor: pointer;">
                            <div class="col-md-3">
                                <img src="${producto.imagen}" class="img-fluid" alt="${producto.nombre}">
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4>${producto.nombre}</h4>
                                        <p>${producto.descripcion}</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="h5">$${producto.precio}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
                contenedorResultados.innerHTML += productoHTML;
            });
        })
        .catch(error => console.error("Error al filtrar productos:", error));
    };

    filtroBusqueda.addEventListener("change", aplicarFiltros);
    filtroCategoria.addEventListener("change", aplicarFiltros);
}); */
