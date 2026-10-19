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

$sql = "SELECT * FROM registro";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Personas</title>
</head>
<body>
    <h1>Consulta de Personas</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Acciones</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id_per"]. "</td><td>" . $row["nombre"]. "</td><td>" . $row["apellido"]. "</td><td>" . $row["dni"]. "</td><td><a href='modificar.php?id=" . $row["id_per"] . "'>Modificar</a> | <a href='borrar.php?id=" . $row["id_per"] . "'>Borrar</a></td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hay registros</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
