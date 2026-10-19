<?php

session_start();


// =========================
// COMPROBAR ADMIN
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: ../login.php");
    exit;
}


// =========================
// OBTENER ID
// =========================

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: panel.php");
    exit;
}


// =========================
// CONEXIÓN A MYSQL
// =========================

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "safefood"
);

if ($conexion->connect_error) {
    die("Error al conectar con la base de datos.");
}

$conexion->set_charset("utf8mb4");


// =========================
// ELIMINAR LUGAR
// =========================

$sql = "
    DELETE FROM lugares
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$stmt->close();

$conexion->close();


// =========================
// VOLVER AL PANEL
// =========================

header("Location: panel.php");
exit;

?>