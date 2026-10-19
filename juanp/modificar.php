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
    $id_per = $_POST['id_per'];
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

    // Consulta SQL para actualizar los datos
    $sql = "UPDATE registro SET 
            nombre='$nombre', 
            apellido='$apellido', 
            dni='$dni', 
            domicilio='$domicilio', 
            email='$email', 
            celular='$celular', 
            contacto='$contacto', 
            docente='$docente', 
            estudiante='$estudiante', 
            nodocente='$nodocente' 
            WHERE id_per='$id_per'";

    if ($conn->query($sql) === TRUE) {
        echo "Registro actualizado correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Ya no es necesario cerrar la conexión aquí, ya que después de enviar los datos, puedes redirigir.
    // El redireccionamiento se maneja con header(), y la conexión se cierra automáticamente al final del script.

    // Redirigir al usuario a la página de consulta después de modificar
    header("Location: consulta.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_per = $_GET['id'];
    $sql = "SELECT * FROM registro WHERE id_per='$id_per'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No se encontró el registro.";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Persona</title>
</head>
<body>
    <h1>Modificar Persona</h1>
    <form method="POST" action="modificar.php">
        <input type="hidden" name="id_per" value="<?php echo htmlspecialchars($row['id_per']); ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required><br><br>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($row['apellido']); ?>" required><br><br>

        <label for="dni">DNI:</label>
        <input type="number" name="dni" value="<?php echo htmlspecialchars($row['dni']); ?>" required><br><br>

        <label for="domicilio">Domicilio:</label>
        <textarea name="domicilio" required><?php echo htmlspecialchars($row['domicilio']); ?></textarea><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br><br>

        <label for="celular">Celular:</label>
        <input type="number" name="celular" value="<?php echo htmlspecialchars($row['celular']); ?>" required><br><br>

        <label for="contacto">Contacto:</label>
        <input type="number" name="contacto" value="<?php echo htmlspecialchars($row['contacto']); ?>" required><br><br>

        <label>Docente:</label>
        <input type="checkbox" name="docente" <?php echo $row['docente'] ? 'checked' : ''; ?>><br><br>

        <label>Estudiante:</label>
        <input type="checkbox" name="estudiante" <?php echo $row['estudiante'] ? 'checked' : ''; ?>><br><br>

        <label>No Docente:</label>
        <input type="checkbox" name="nodocente" <?php echo $row['nodocente'] ? 'checked' : ''; ?>><br><br>

        <button type="submit">Modificar</button>
    </form>
</body>
</html>
