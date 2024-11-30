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
