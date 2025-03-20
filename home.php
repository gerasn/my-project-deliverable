<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/estilosBootstrap.css">
</head>
<body>
    <!-- Contenedor principal -->
    <div class="container-fluid p-0">
        <!-- Header -->
        <header class="row bg-primary text-white p-3 align-items-center">
            <div class="col-12 col-md-1 text-center">
            </div>
            <div class="col-12 col-md-10">
                <h1 class="h4 mb-0">Sistema de Gestión</h1>
            </div>
            <div class="col-12 col-md-1 text-end">
                <span class="fw-bold">Versión 0.1</span>
            </div>
        </header>

        <!-- Menú lateral -->
        <button id="menuToggleBtn" class="btn btn-primary d-md-none my-3" type="button" data-bs-toggle="collapse" data-bs-target="#menuLateral" aria-expanded="false" aria-controls="menuLateral">
            ☰ Menú
        </button>

        <div class="row">
            <!-- Menú lateral -->
            <nav class="collapse d-md-block col-12 col-md-2 bg-light border-end p-3" id="menuLateral">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark" data-target="formulario.php">Crear nueva landing</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-dark" data-target="lista.php">Listar landings</a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger">Salir</a>
                    </li>
                </ul>
            </nav>

            <!-- Contenido principal -->
            <main class="col-12 col-md-10 p-4 position-relative">
                <div id="background" class="background-image-container">
                    <img src="assets/pexels-shkrabaanthony-5475763.jpg" alt="Fondo del home" class="background-image">
                </div>

                <!-- Contenedor dinámico -->
                <div id="dynamic-content" class="content-wrapper">
                    <h2>Bienvenido, Usuario</h2>
                    <p class="lead">Selecciona una opción del menú lateral para comenzar.</p>
                </div>

                <!-- Contenedor de mensajes -->
                <div id="message-container" class="alert d-none"></div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Cargar contenido dinámico al hacer clic en los enlaces del menú
    document.querySelectorAll('[data-target]').forEach(link => {
        link.addEventListener('click', async (event) => {
            event.preventDefault();  // Prevenir la redirección estándar de enlaces
            const target = event.target.getAttribute('data-target');  // Obtener el archivo de destino

            try {
                // Ocultar la imagen de fondo cuando se carga un nuevo contenido
                document.getElementById('background').style.display = 'none';

                // Cargar el contenido dinámicamente usando fetch()
                const response = await fetch(target);
                if (!response.ok) throw new Error(`Error al cargar: ${response.statusText}`);
                const content = await response.text();  // Obtener el contenido de la página solicitada

                // Insertar el contenido cargado dentro del contenedor dinámico
                document.getElementById('dynamic-content').innerHTML = content;

                // Verificar si el contenido cargado es un formulario
                if (target === 'formulario.php') {
                    // Asociar el formulario a un manejador de eventos para enviar datos mediante AJAX
                    document.getElementById('formulario').addEventListener('submit', function(event) {
                        event.preventDefault();  // Evitar el envío tradicional del formulario

                        var formData = new FormData(this);

                        fetch(target, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())  // Esperar la respuesta como JSON
                        .then(data => {
                            const messageContainer = document.getElementById('message-container');
                            if (data.status === 'success') {
                                messageContainer.className = 'alert alert-success';
                                messageContainer.innerHTML = `
                                    <p>${data.message}</p>
                                    ${data.url ? `<p>URL generada: <a href="${data.url}" target="_blank">${data.url}</a></p>` : ''}
                                `;
                            } else {
                                messageContainer.className = 'alert alert-danger';
                                messageContainer.textContent = data.message;
                            }
                            messageContainer.classList.remove('d-none');
                            document.getElementById('formulario').reset();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            const messageContainer = document.getElementById('message-container');
                            messageContainer.className = 'alert alert-danger';
                            messageContainer.textContent = 'Ocurrió un error al enviar el formulario.';
                            messageContainer.classList.remove('d-none');
                        });
                    });
                } else {
                    // Mostrar el fondo solo si volvemos al home
                    if (target === 'home.php') {
                        document.getElementById('background').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error(error);
                document.getElementById('dynamic-content').innerHTML = `<p class="text-danger">Ocurrió un error al cargar el contenido. Intenta nuevamente.</p>`;
            }
        });
    });
    </script>
</body>
</html>
