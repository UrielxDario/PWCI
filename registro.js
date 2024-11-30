document.addEventListener('DOMContentLoaded', function () {
    const formulario = document.querySelector('form');
    const inputCorreo = document.getElementById('email');
    const inputNombreUsuario = document.getElementById('username');
    const inputContrasena = document.getElementById('password');
    const seleccionRol = document.getElementById('role');
    const divPrivacidad = document.getElementById('privacidadDiv');
    const inputAvatar = document.getElementById('avatar');
    const inputApellidoPaterno = document.getElementById('apellido_paterno');
    const inputApellidoMaterno = document.getElementById('apellido_materno');
    const inputNombres = document.getElementById('nombres');
    const inputFechaNacimiento = document.getElementById('birthdate');
    const seleccionGenero = document.getElementById('gender');
    
   
    let alertaCorreoMostrada = false;
    let alertaUsuarioMostrada = false;

    // Verificar si el correo ya existe al perder el foco del campo de correo
    function verificarCorreoCompleto() {
        const valorCorreo = inputCorreo.value.trim();
        const emailDomain = document.getElementById("Dominio").value.trim(); 
    
        const correoCompleto = valorCorreo + emailDomain;
    
        if (valorCorreo !== '' && emailDomain !== '') {
            verificarDisponibilidad('correo', correoCompleto, function (respuesta) {
                if (respuesta.existe) {
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


    inputNombreUsuario.addEventListener('blur', function () {
        const valorUsuario = inputNombreUsuario.value.trim();
        if (valorUsuario !== '') {
            verificarDisponibilidad('username', valorUsuario, function (respuesta) {
                if (respuesta.existe) {
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
        if (inputAvatar.files.length === 0) {
            alert('Por favor, selecciona una imagen de avatar.');
            event.preventDefault();
            return; 
        } else {
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
});


/*PARA MOSTRAR LA IMAGEN EN EL REGISTRO*/ 
document.getElementById("avatar").addEventListener("change", function(event) {
    const ImagenVprevia = document.getElementById("ImagenAvatar");
    const previewContainer = document.getElementById("ContainerAvatarRegistro");

    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function(e) {
            ImagenVprevia.src = e.target.result;
            ImagenVprevia.style.display = "block";
        };

        reader.readAsDataURL(file);
    } else {
        ImagenVprevia.style.display = "none";
    }
});