function cambioprecio() {
    var check = document.getElementById("cotizacionCheck");
    var campoprecio = document.getElementById("precioProducto");
    if (check.checked) {
        campoprecio.value = ""; 
        campoprecio.disabled = true; 
    } else {
        campoprecio.disabled = false; 
    }
}

function validarFormulario() {
    var form = document.getElementById("formularioproducto");
    if (!form.checkValidity()) {
        alert("Por favor, llena todos los campos obligatorios.");
        return false; 
    }
    return true;
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

            actualizarCategorias();
            window.location.href = 'PublicarProducto.php';

        } else {
            alert('Hubo un problema al crear la categoría');
        }
    })
    .catch(error => console.error('Error:', error));
}

function actualizarCategorias() {
    fetch('PublicarProducto.php') // Este archivo PHP debería devolver las categorías actualizadas en formato JSON
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const categoriaSelect = document.getElementById('categoriaProducto');
            categoriaSelect.innerHTML = ''; // Limpiar las opciones actuales


            // Agregar las nuevas categorías
            data.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.ID_CATEGORIA;
                option.textContent = categoria.NombreCategoria;
                categoriaSelect.appendChild(option);
            });

        } else {
            console.error('Error al obtener categorías', data.error);
        }
    })
    .catch(error => console.error('Error al actualizar categorías:', error));
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formularioproducto");
    const nombreProducto = document.getElementById("nombreProducto");
    const descripcionProducto = document.getElementById("descripcionProducto");
    const precioProducto = document.getElementById("precioProducto");
    const categoriaProducto = document.getElementById("categoriaProducto");
    const cantidadProducto = document.getElementById("cantidadProducto");
    const productImages = document.getElementById("productImages");
    const productVideo = document.getElementById("productVideo");
    const cotizacionCheck = document.getElementById("cotizacionCheck");

    // Validaciones
    form.addEventListener("submit", function (event) {

        if (nombreProducto.value.trim() === "") {
            alert("Por favor, ingresa el nombre del producto.");
            event.preventDefault();
            return;
        }

        if (descripcionProducto.value.trim() === "") {
            alert("Por favor, ingresa una descripción del producto.");
            event.preventDefault();
            return;
        }

        if (!cotizacionCheck.checked) {
            if (precioProducto.value === "" || isNaN(precioProducto.value) || parseFloat(precioProducto.value) <= 0) {
                alert("El precio debe ser un número positivo.");
                event.preventDefault();
                return;
            }

            if (parseInt(cantidadProducto.value) < 1) {
                alert("La cantidad debe ser un número mayor a 0.");
                event.preventDefault();
                return;
            }
        }

        if (categoriaProducto.value === "") {
            alert("Por favor, selecciona una categoría.");
            event.preventDefault();
            return;
        }

        if (productImages.files.length !== 3) {
            alert("Debes seleccionar exactamente 3 imágenes.");
            event.preventDefault();
            return;
        }

        const allowedImageFormats = ["image/jpeg", "image/png", "image/jpg"];
        for (let i = 0; i < productImages.files.length; i++) {
            if (!allowedImageFormats.includes(productImages.files[i].type)) {
                alert("Cada imagen debe estar en formato .jpg, .jpeg o .png.");
                event.preventDefault();
                return;
            }
        }

        if (productVideo.files.length !== 1) {
            alert("Debes seleccionar exactamente 1 video.");
            event.preventDefault();
            return;
        }

        const allowedVideoFormats = ["video/mp4", "video/avi", "video/mov"];
        if (!allowedVideoFormats.includes(productVideo.files[0].type)) {
            alert("El video debe estar en formato .mp4, .avi o .mov.");
            event.preventDefault();
            return;
        }
    });

    cotizacionCheck.addEventListener("change", function () {
        if (cotizacionCheck.checked) {
            precioProducto.disabled = true;
            cantidadProducto.disabled = true;
        } else {
            precioProducto.disabled = false;
            cantidadProducto.disabled = false;
        }
    });
});



document.addEventListener("DOMContentLoaded", function () {
    const productImages = document.getElementById("productImages");
    const productVideo = document.getElementById("productVideo");
    const previewImages = document.getElementById("previewImages");
    const previewVideo = document.getElementById("previewVideo");

    // Mostrar vista previa de las imágenes seleccionadas
    productImages.addEventListener("change", function () {
        previewImages.innerHTML = ""; // Limpiar la vista previa existente
        const files = productImages.files;

        if (files.length === 3) {
            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.style.width = "400px";
                    img.style.height = "400px";
                    img.classList.add("border", "border-secondary", "rounded");
                    previewImages.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        } else {
            alert("Debes seleccionar exactamente 3 imágenes.");
        }
    });

    // Mostrar vista previa del video seleccionado
    productVideo.addEventListener("change", function () {
        previewVideo.innerHTML = ""; // Limpiar la vista previa existente
        const file = productVideo.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const video = document.createElement("video");
                video.src = e.target.result;
                video.controls = true;
                video.style.width = "600px";
                video.style.height = "600px";
                previewVideo.appendChild(video);
            };
            reader.readAsDataURL(file);
        }
    });
});
