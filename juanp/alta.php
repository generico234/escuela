<?php
$servername = "localhost";
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "personas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $domicilio = $_POST['domicilio'];
    $email = $_POST['email'];
    $celular = $_POST['celular'];
    $contacto = $_POST['contacto'];
    $docente = isset($_POST['docente']) ? 1 : 0;
    $estudiante = isset($_POST['estudiante']) ? 1 : 0;
    $nodocente = isset($_POST['nodocente']) ? 1 : 0;

    $sql = "INSERT INTO registro (nombre, apellido, dni, domicilio, email, celular, contacto, docente, estudiante, nodocente)
            VALUES ('$nombre', '$apellido', '$dni', '$domicilio', '$email', '$celular', '$contacto', '$docente', '$estudiante', '$nodocente')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Persona</title>
</head>
<body>
    <h1>Registrar Nueva Persona</h1>
    <form method="POST" action="alta.php">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required><br><br>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" required><br><br>

        <label for="dni">DNI:</label>
        <input type="number" name="dni" required><br><br>

        <label for="domicilio">Domicilio:</label>
        <textarea name="domicilio" required></textarea><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="celular">Celular:</label>
        <input type="number" name="celular" required><br><br>

        <label for="contacto">Contacto:</label>
        <input type="number" name="contacto" required><br><br>

        <label>Docente:</label>
        <input type="checkbox" name="docente"><br><br>

        <label>Estudiante:</label>
        <input type="checkbox" name="estudiante"><br><br>

        <label>No Docente:</label>
        <input type="checkbox" name="nodocente"><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
