<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// =========================
// COMPROBAR USUARIO NORMAL
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "usuario"
) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Tenés que iniciar sesión con una cuenta de usuario."
    ]);

    exit;
}


// =========================
// CONEXIÓN A MYSQL
// =========================

require_once "conexion.php";


// =========================
// RECIBIR DATOS
// =========================

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$datos) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se recibieron datos."
    ]);

    exit;
}


$idLugar = intval(
    $datos["id_lugar"] ?? 0
);

$comentario = trim(
    $datos["comentario"] ?? ""
);


// =========================
// VALIDAR
// =========================

if ($idLugar <= 0) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Lugar inválido."
    ]);

    exit;
}


if ($comentario === "") {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Escribí un comentario."
    ]);

    exit;
}


// =========================
// INSERTAR COMENTARIO
// =========================

$idUsuario = $_SESSION["usuario_id"];


$sql = "
    INSERT INTO comentarios
    (
        id_lugar,
        id_usuario,
        comentario
    )
    VALUES (?, ?, ?)
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Error preparando la consulta."
    ]);

    exit;
}


$stmt->bind_param(
    "iis",
    $idLugar,
    $idUsuario,
    $comentario
);


if (!$stmt->execute()) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo guardar el comentario."
    ]);

    exit;
}


// =========================
// RESPUESTA
// =========================

echo json_encode([
    "exito" => true,
    "mensaje" => "Comentario publicado correctamente."
]);


$stmt->close();
$conexion->close();

exit;

?>