<?php

include('ConexionBDGeman.php'); // Nombre correcto del archivo

session_start();

// Inicializa el mensaje desde la sesión si existe
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Limpia la variable de sesión después de usarla

// Configuración de la base de datos
$host = "127.0.0.1";
$usuario = "root";
$contrasena = "";
$base_de_datos = "entregable";

// Instancia y conexión a la base de datos
$db = new ConexionBD($host, $usuario, $contrasena, $base_de_datos);
$db->conectar(); // Inicializa la conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            // Prepara y ejecuta la consulta
            $query = $db->conexion->prepare("SELECT * FROM users WHERE username = :username");
            $query->bindParam(":username", $username, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Verifica si se encontró el usuario
            if (!$result) {
                $_SESSION['message'] = '¡El usuario ingresado no existe!';
            } else {
                // Verifica la contraseña
                if (password_verify($password, $result['password'])) {
                    $_SESSION['user_id'] = $result['id'];
                    echo '<p class="success">¡Felicidades, has iniciado sesión correctamente!</p>';
                    // Redirige a la página principal
                    header("Location: home.php");
                    exit();
                } else {
                    $_SESSION['message'] = '¡La contraseña ingresada es incorrecta!';
                }
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error en la base de datos: " . $e->getMessage();
        }

        // Redirige después de procesar para limpiar POST
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/estilosMaterialize.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</header>

<body>
    <div class="grey_lighten-4">
        <div class="container">
            <div class="card z-depth-3">
                <h5 class="card-title">Inicia Sesión</h5>

                <form method="post" action="" name="signin-form">
                    <div class="input-field">
                        <i class="material-icons prefix">person</i>
                        <input id="username" type="text" name="username" pattern="[a-zA-Z0-9]+" required>
                        <label for="username">Usuario</label>
                    </div>
                    <div class="input-field">
                        <i class="material-icons prefix">https</i>
                        <input id="password" type="password" name="password" required>
                        <label for="password">Contraseña</label>
                    </div>
                    <div class="button">
                        <button type="submit" name="login" value="login" class="btn waves-effect waves-light">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para mensajes de error -->
    <div id="error-modal" class="modal">
        <div class="modal-content">
            <h4>Error</h4>
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-purple btn-flat">Cerrar</a>
        </div>
    </div>

    <!-- Modal para recuperación de contraseña -->
    <div id="modal1" class="modal">
        <div class="modal-content">
            <h4>Recuperar Contraseña</h4>
            <form method="post" action="procesar_recuperacion.php">
                <div class="input-field">
                    <i class="material-icons prefix">email</i>
                    <input id="email" type="email" name="email" required>
                    <label for="email">Correo Electrónico</label>
                </div>
                <div class="button">
                    <button type="submit" name="recover" value="recover" class="btn waves-effect waves-light">Enviar Enlace</button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-purple btn-flat">Cerrar</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        // Inicializa los modales de Materialize
        document.addEventListener('DOMContentLoaded', function () {
            var elems = document.querySelectorAll('.modal');
            M.Modal.init(elems);

            // Si hay un mensaje de error, abre el modal
            <?php if (!empty($message)): ?>
                var errorModal = document.querySelector('#error-modal');
                var instance = M.Modal.getInstance(errorModal);
                instance.open();
            <?php endif; ?>
        });
    </script>
</body>


</html>
