document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.getElementById('formulario');
    const inputCorreo = document.getElementById('email');
    const inputNombreUsuario = document.getElementById('username');
    const inputContrasena = document.getElementById('password');
    const seleccionRol = document.getElementById('role');
    const divPrivacidad = document.getElementById('privacidadDiv');
    const inputAvatar = document.getElementById('avatar');
    const imagePreview = document.getElementById('imagePreview');
    const inputApellidoPaterno = document.getElementById('apellido_paterno');
    const inputApellidoMaterno = document.getElementById('apellido_materno');
    const inputNombres = document.getElementById('nombres');
    const inputFechaNacimiento = document.getElementById('birthdate');
    const seleccionGenero = document.getElementById('gender');

    const usuarioIdActual = document.getElementById('usuarioIdActual').value;
    console.log('ID de usuario actual:', usuarioIdActual); 


    

    let alertaCorreoMostrada = false;
    let alertaUsuarioMostrada = false;

    // Verificar si el correo ya existe al perder el foco del campo de correo
    function verificarCorreoCompleto() {
        const valorCorreo = inputCorreo.value.trim();
        const emailDomain = document.getElementById("Dominio").value.trim(); 
        const correoCompleto = valorCorreo + emailDomain;
    
        if (valorCorreo !== '' && emailDomain !== '') {
            verificarDisponibilidad('correo', correoCompleto, function (respuesta) {
                // Si el correo no es el mismo que el actual, y está en uso, mostrar alerta
                if (respuesta.existe && respuesta.usuarioId !== usuarioIdActual) {
                    if (!alertaCorreoMostrada) {
                        alert('El correo ya está en uso. Por favor, elige otro.');
                        alertaCorreoMostrada = true; 
                    }
                    inputCorreo.focus();
                } else {
                    alertaCorreoMostrada = false; 
                }
            });
        } else {
            alertaCorreoMostrada = false; 
        }
    }
    
    inputCorreo.addEventListener('blur', verificarCorreoCompleto);
    document.getElementById("Dominio").addEventListener('change', verificarCorreoCompleto);
    
    // Verificar si el nombre de usuario ya existe al perder el foco del campo de nombre de usuario
    inputNombreUsuario.addEventListener('blur', function () {
        const valorUsuario = inputNombreUsuario.value.trim();
        if (valorUsuario !== '') {
            verificarDisponibilidad('username', valorUsuario, function (respuesta) {
                // Si el nombre de usuario no es el mismo que el actual, y está en uso, mostrar alerta
                if (respuesta.existe && respuesta.usuarioId !== usuarioIdActual) {
                    if (!alertaUsuarioMostrada) {
                        alert('El nombre de usuario ya está en uso. Por favor, elige otro.');
                        alertaUsuarioMostrada = true; 
                    }
                    inputNombreUsuario.focus();
                } else {
                    alertaUsuarioMostrada = false; 
                }
            });
        } else {
            alertaUsuarioMostrada = false; 
        }
    });
    
    // Función AJAX para verificar la disponibilidad en el servidor
    function verificarDisponibilidad(campo, valor, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ComprobarExistenciaCorreo.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                console.log(xhr.responseText);  // Añadir esta línea para ver la respuesta del servidor

                const respuesta = JSON.parse(xhr.responseText);
                callback(respuesta);
            }
        };
        xhr.send(campo + '=' + encodeURIComponent(valor));
    }






    // Mostrar/ocultar opciones de privacidad según el rol
    seleccionRol.addEventListener('change', function () {
        var rolSeleccionado = this.value;

        if (rolSeleccionado === 'Cliente') {
            divPrivacidad.style.display = 'block';
        } else {
            divPrivacidad.style.display = 'none';

            document.querySelectorAll('input[name="privacidad"]').forEach(function (radio) {
                radio.checked = false;
            });
        }
    });

    // Previsualizar imagen de avatar
    inputAvatar.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result; // Establece la ruta de la imagen
                imagePreview.style.display = 'block'; // Muestra la imagen
            };
            reader.readAsDataURL(file); // Carga la imagen
        }
    });

    

    // Validaciones
    formulario.addEventListener('submit', function (event) {

        // Validación de correo electrónico
        const valorCorreo = inputCorreo.value.trim();
        const DominioCorreo = document.getElementById("Dominio").value.trim(); 

        const SimbolosNOCorreo = /[@.]/;

        if (valorCorreo === '') {
            alert("Por favor, ingresa un correo electrónico válido.");
            event.preventDefault();
            return; 
        } 
        else if (SimbolosNOCorreo.test(valorCorreo)) {
            alert("El campo de correo no debe incluir '@' o '.' ");
            event.preventDefault();
            return;
        } 
        else{
        const EmailCompleto = valorCorreo + DominioCorreo;
        document.getElementById("correoCompleto").value = EmailCompleto;
        }


        // Validación de nombre de usuario (mínimo 3 caracteres)
        if (inputNombreUsuario.value.length < 3) {
            alert('El nombre de usuario debe tener al menos 3 caracteres.');
            event.preventDefault();
            return; 
        }

        // Validación de la contraseña (mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial)
        const patronContrasena = /^(?=.*[a-zñ])(?=.*[A-ZÑ])(?=.*\d)(?=.*[\W_]).{8,}$/;
        if (!patronContrasena.test(inputContrasena.value)) {
            alert('La contraseña debe tener al menos 8 caracteres, incluyendo mayúscula, minúscula, número y carácter especial.');
            event.preventDefault();
            return; 
        }
        

        // Validación del rol
        if (seleccionRol.value === '') {
            alert('Por favor, selecciona un rol.');
            event.preventDefault();
            return; 
        }

        // Validación si el rol es cliente
        if (seleccionRol.value === 'Cliente') {
            const privacidadChecked = Array.from(document.querySelectorAll('input[name="privacidad"]:checked')).length > 0;
            if (!privacidadChecked) {
                alert('Por favor, selecciona una opción de privacidad.');
                event.preventDefault();
                return;
            }
        }

        // Validación del avatar (asegurar que sea una imagen)
        if (inputAvatar.files.length === 0 && !document.getElementById('currentImageName').textContent) {
            alert('Por favor, selecciona una imagen de avatar o mantén la imagen actual.');
            event.preventDefault();
            return; 
        } else if (inputAvatar.files.length > 0) {
            const extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            const extensionArchivo = inputAvatar.files[0].name.split('.').pop().toLowerCase();
            if (!extensionesPermitidas.includes(extensionArchivo)) {
                alert('Por favor, selecciona un archivo de imagen válido (jpg, jpeg, png, gif).');
                event.preventDefault();
                return; 
            }
        }


        
        // Validación de apellido paterno y materno
        const valSOLOLETRAS = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

        if (!valSOLOLETRAS.test(inputApellidoPaterno.value.trim())) {
            alert('El apellido paterno solo puede contener letras y espacios.');
            event.preventDefault();
            return;
        }

        if (!valSOLOLETRAS.test(inputApellidoMaterno.value.trim())) {
            alert('El apellido materno solo puede contener letras y espacios.');
            event.preventDefault();
            return;
        }

        // Validación de nombres
        if (!valSOLOLETRAS.test(inputNombres.value.trim())) {
            alert('El nombre solo puede contener letras y espacios.');
            event.preventDefault();
            return;
        }



        // Validación de la fecha de nacimiento
        if (inputFechaNacimiento.value === '') {
            alert('Por favor, selecciona tu fecha de nacimiento.');
            event.preventDefault();
            return; 
        } else {
            const fechaNacimiento = new Date(inputFechaNacimiento.value);
            const hoy = new Date();
            if (fechaNacimiento > hoy) {
                alert('Por favor, selecciona una fecha de nacimiento válida (no puede ser futura).');
                event.preventDefault();
                return; 
            }
        }

        // Validación del sexo
        if (seleccionGenero.value === '') {
            alert('Por favor, selecciona tu sexo.');
            event.preventDefault();
            return; 
        }
    });

    fetch('api/get_user_data.php')
    .then(response => {
        console.log('Respuesta del servidor:', response);  
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);  

        if (data.error) {
            console.error(data.error);
        } else {
            document.getElementById('username').value = data.Username;
            document.getElementById('password').value = data.Contraseña;
            document.getElementById('apellido_paterno').value = data.ApellidoPaterno;
            document.getElementById('apellido_materno').value = data.ApellidoMaterno;
            document.getElementById('nombres').value = data.Nombre;
            document.getElementById('birthdate').value = data.FechaNacimiento;
            document.getElementById('gender').value = data.Sexo;
            document.getElementById('role').value = data.Rol;

            seleccionRol.dispatchEvent(new Event('change'));

            if (data.PrivacidadUsuario === 'publico') {
                document.querySelector('input[name="privacidad"][value="publico"]').checked = true;
            } else {
                document.querySelector('input[name="privacidad"][value="privado"]').checked = true;
            }

            const imagePreview = document.getElementById('imagePreview');

            const currentImageName = document.getElementById('currentImageName');

            imagePreview.src = 'img/' + data.ImgPerfil; 
            imagePreview.style.display = 'block';
            currentImageName.textContent = 'Imagen actual: ' + data.ImgPerfil; // Mostrar nombre de archivo actual


            if (data.Correo && data.Correo.includes('@')) {
                const [emailUser, emailDomain] = data.Correo.split('@');

                console.log('Usuario del correo:', emailUser);
                console.log('Dominio del correo:', emailDomain);

                document.getElementById('email').value = emailUser;

                const emailDomainSelect = document.getElementById('Dominio');
                const domainOption = "@" + emailDomain;  

                if ([...emailDomainSelect.options].some(option => option.value === domainOption)) {
                    emailDomainSelect.value = domainOption;
                } else {
                    console.warn("El dominio no coincide con ninguna opción en el combobox.");
                }
            } else {
                console.error("Correo no es válido o no contiene '@'");
            }
        }
    })
    .catch(error => {
        console.error('Error al obtener datos del usuario:', error);
    });

    



});

document.getElementById('avatar').addEventListener('change', function (event) {
    const imagePreview = document.getElementById('imagePreview');
    const currentImageName = document.getElementById('currentImageName');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block'; 
            currentImageName.textContent = ''; 
        };

        reader.readAsDataURL(file);
    }
});



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
