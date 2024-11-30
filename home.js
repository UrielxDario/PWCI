function createList() {
    const name = document.getElementById('listName').value;
    const description = document.getElementById('listDescription').value;
    const isPublic = document.getElementById('isPublic').checked;

    console.log('Lista creada:', { name, description, isPublic });

    
    const modal = bootstrap.Modal.getInstance(document.getElementById('createListModal'));
    modal.hide();

    
    document.getElementById('createListForm').reset();
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
