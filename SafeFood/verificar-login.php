<?php

session_start();

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

if ($usuario === "" || $contrasena === "") {
    header("Location: login.php?error=campos");
    exit;
}


/* BUSCAR USUARIO */

$sql = "SELECT id, usuario, contrasena, rol
        FROM usuarios
        WHERE usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();


/* USUARIO NO EXISTE */

if ($resultado->num_rows !== 1) {

    header("Location: login.php?error=datos");
    exit;
}


$datos = $resultado->fetch_assoc();


/* COMPROBAR CONTRASEÑA */

if (!password_verify($contrasena, $datos["contrasena"])) {

    header("Location: login.php?error=datos");
    exit;
}


/* CREAR SESIÓN */

session_regenerate_id(true);

$_SESSION["usuario_id"] = $datos["id"];
$_SESSION["usuario"] = $datos["usuario"];
$_SESSION["rol"] = $datos["rol"];


/* VOLVER A SAFEFOOD */

header("Location: index.html");
exit;