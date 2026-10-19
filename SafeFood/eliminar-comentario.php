<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

// =========================
// COMPROBAR USUARIO NORMAL
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"])
) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Tenés que iniciar sesión."
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

$idComentario = intval(
    $datos["id_comentario"] ?? 0
);

if ($idComentario <= 0) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Comentario inválido."
    ]);

    exit;
}

// =========================
// ELIMINAR SOLO SI ES DEL USUARIO
// =========================

$idUsuario = $_SESSION["usuario_id"];
$rol = $_SESSION["rol"];

// =========================
// ADMIN
// =========================

if ($rol === "admin") {

    $sql = "
        DELETE FROM comentarios
        WHERE id = ?
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
        "i",
        $idComentario
    );


// =========================
// USUARIO NORMAL
// =========================

} elseif ($rol === "usuario") {

    $sql = "
        DELETE FROM comentarios
        WHERE id = ?
        AND id_usuario = ?
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
        "ii",
        $idComentario,
        $idUsuario
    );


// =========================
// OTRO ROL
// =========================

} else {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No tenés permisos para eliminar comentarios."
    ]);

    exit;
}

$stmt->execute();

if ($stmt->affected_rows === 0) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "El comentario no existe o no tenés permisos para eliminarlo."
    ]);

    exit;
}

// =========================
// RESPUESTA
// =========================

echo json_encode([
    "exito" => true,
    "mensaje" => "Comentario eliminado correctamente."
]);

$stmt->close();
$conexion->close();

exit;
?>