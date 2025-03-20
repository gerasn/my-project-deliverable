<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/estilosBootstrap.css"> <!-- Tu CSS personalizado -->
</head>
<body>

<div class="container mt-4">
    <div class="listar-container">
        <h2 class="mb-3 text-center">Listado de Empleados</h2>

        <?php
        // Configuración de conexión a la base de datos
        $host = '127.0.0.1';
        $dbname = 'entregable';
        $username = 'root';
        $password = '';

        try {
            // Establecer conexión con la base de datos
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Obtener los días festivos de Colombia desde la API
            $year = date("Y");
            $api_url = "https://api-colombia.com/api/v1/holiday/year/$year";
            $festivos = json_decode(file_get_contents($api_url), true);

            // Convertir festivos a un array de fechas
            $dias_festivos = [];
            if ($festivos) {
                foreach ($festivos as $festivo) {
                    $dias_festivos[] = $festivo['date']; // Formato: "YYYY-MM-DD"
                }
            }

            // Consulta a la vista
            $query = "SELECT usuario_id, nombre, correo, cargo, fecha_ingreso, contrato FROM view_usuarios";
            $stmt = $pdo->query($query);

            if ($stmt->rowCount() > 0) {
                // Mostrar datos en tabla
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-primary text-center'>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Cargo</th>
                            <th>Fecha de Ingreso</th>
                            <th>Días Trabajados</th>
                            <th>Contrato</th>
                            <th>Acciones</th>
                        </tr>
                      </thead>";
                echo "<tbody>";

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Calcular los días hábiles trabajados
                    $fecha_ingreso = new DateTime($row['fecha_ingreso']);
                    $hoy = new DateTime();
                    $dias_habiles = 0;

                    while ($fecha_ingreso <= $hoy) {
                        $dia_semana = $fecha_ingreso->format('N'); // 1=Lunes, 7=Domingo
                        $fecha_str = $fecha_ingreso->format('Y-m-d');

                        if ($dia_semana < 6 && !in_array($fecha_str, $dias_festivos)) {
                            $dias_habiles++; // Contar solo si es día hábil
                        }
                        $fecha_ingreso->modify('+1 day'); // Avanzar al siguiente día
                    }

                    echo "<tr>";
                    echo "<td class='text-center'>{$row['usuario_id']}</td>";
                    echo "<td>{$row['nombre']}</td>";
                    echo "<td>{$row['correo']}</td>";
                    echo "<td>{$row['cargo']}</td>";
                    echo "<td class='text-center'>{$row['fecha_ingreso']}</td>";
                    echo "<td class='text-center'>{$dias_habiles}</td>";

                    // Botón para visualizar el contrato PDF
                    if (!empty($row['contrato'])) {
                        echo "<td class='text-center'><a href='uploads/{$row['contrato']}' target='_blank' class='btn btn-sm btn-success'>Ver Contrato</a></td>";
                    } else {
                        echo "<td class='text-center text-muted'>Sin contrato</td>";
                    }

                    // Botones de acciones (Editar y Eliminar)
                    echo "<td class='text-center'>
                            <a href='editar.php?id={$row['usuario_id']}' class='btn btn-sm btn-warning'>Editar</a>
                            <a href='eliminar.php?id={$row['usuario_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar este registro?\")'>Eliminar</a>
                          </td>";

                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<p class='alert alert-warning text-center'>No hay empleados registrados.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='alert alert-danger text-center'>Error de conexión: " . $e->getMessage() . "</p>";
        } catch (Exception $e) {
            echo "<p class='alert alert-danger text-center'>Error al calcular días hábiles: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
