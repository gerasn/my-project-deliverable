<?php
// Conexión a la base de datos
$conn = new mysqli("127.0.0.1", "root", "", "entregable");

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . $conn->connect_error]));
}

// Obtener lista de roles para el select
$roles = $conn->query("SELECT id, nombre_cargo FROM roles");

// Manejar el formulario al enviarlo
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'] ?? null;
    $correo = $_POST['correo'] ?? null;
    $id_rol = $_POST['cargo'] ?? null;
    $fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
    $contrato = "Indefinido"; // Valor por defecto

    if (empty($nombre) || empty($correo) || empty($id_rol) || empty($fecha_ingreso)) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }

    // Preparar la consulta
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo_electronico, id_rol, fecha_ingreso, contrato) VALUES (?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("ssiss", $nombre, $correo, $id_rol, $fecha_ingreso, $contrato);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Usuario registrado exitosamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta: ' . $conn->error]);
    }

    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Registro de Usuario</h2>
        <form id="formulario" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <input type="email" name="correo" id="correo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo:</label>
                <select name="cargo" id="cargo" class="form-control" required>
                    <option value="">Selecciona un cargo</option>
                    <?php while ($row = $roles->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre_cargo']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
        <div id="resultado" class="mt-3"></div>
    </div>

    <script>
        document.getElementById('formulario').addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);
            fetch('', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                document.getElementById('resultado').innerHTML = `<div class="alert alert-${data.status === 'success' ? 'success' : 'danger'}">${data.message}</div>`;
                if (data.status === 'success') this.reset();
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
