<?php

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: registro.php");
    exit;
}

$usuario = trim($_POST["usuario"] ?? "");
$contrasena = $_POST["contrasena"] ?? "";

if ($usuario === "" || $contrasena === "") {
    die("Completá todos los campos.");
}

if (strlen($usuario) < 3) {
    die("El usuario debe tener al menos 3 caracteres.");
}

if (strlen($contrasena) < 6) {
    die("La contraseña debe tener al menos 6 caracteres.");
}


/* COMPROBAR SI EL USUARIO YA EXISTE */

$sql = "SELECT id FROM usuarios WHERE usuario = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    die("Ese usuario ya está registrado.");
}


/* ENCRIPTAR CONTRASEÑA */

$hash = password_hash($contrasena, PASSWORD_DEFAULT);


/* GUARDAR USUARIO */

/* TIPO DE CUENTA */

$rol = $_POST["rol"] ?? "usuario";

if ($rol !== "usuario" && $rol !== "empresa") {
    $rol = "usuario";
}


/* GUARDAR USUARIO */

$sql = "INSERT INTO usuarios (usuario, contrasena, rol)
        VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sss", $usuario, $hash, $rol);

if ($stmt->execute()) {

    header("Location: login.php?registro=ok");
    exit;

} else {

    die("No se pudo crear la cuenta.");
}