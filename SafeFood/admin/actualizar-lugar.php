<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

// =========================
// COMPROBAR ADMIN
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No autorizado"
    ]);

    exit;
}

// =========================
// CONEXIÓN A MYSQL
// =========================

require_once "../conexion.php";

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

$id = intval(
    $datos["id"] ?? 0
);

$nombre = trim(
    $datos["nombre"] ?? ""
);

$horario = trim(
    $datos["horario"] ?? ""
);

$descripcion = trim(
    $datos["descripcion"] ?? ""
);

$latitud =
    $datos["latitud"] ?? null;

$longitud =
    $datos["longitud"] ?? null;

$intolerancias =
    $datos["intolerancias"] ?? [];

// =========================
// VALIDAR
// =========================

if (
    $id <= 0 ||
    $nombre === "" ||
    $horario === "" ||
    $latitud === null ||
    $longitud === null
) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Faltan datos obligatorios."
    ]);

    exit;
}

// =========================
// ACTUALIZAR LUGAR
// =========================

$stmt = $conexion->prepare(
    "UPDATE lugares
     SET nombre = ?,
         latitud = ?,
         longitud = ?,
         horario = ?,
         descripcion = ?
     WHERE id = ?"
);

if (!$stmt) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Error preparando la actualización."
    ]);

    exit;
}

$stmt->bind_param(
    "sddssi",
    $nombre,
    $latitud,
    $longitud,
    $horario,
    $descripcion,
    $id
);

if (!$stmt->execute()) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo actualizar el lugar."
    ]);

    exit;
}

$stmt->close();

// =========================
// BORRAR INTOLERANCIAS ANTERIORES
// =========================

$stmtEliminar = $conexion->prepare(
    "DELETE FROM lugar_intolerancia
     WHERE id_lugar = ?"
);

if (!$stmtEliminar) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Error actualizando las características."
    ]);

    exit;
}

$stmtEliminar->bind_param(
    "i",
    $id
);

$stmtEliminar->execute();

$stmtEliminar->close();

// =========================
// GUARDAR NUEVAS INTOLERANCIAS
// =========================

if (!empty($intolerancias)) {

    $stmtRelacion = $conexion->prepare(
        "INSERT INTO lugar_intolerancia
         (id_lugar, id_intolerancia)
         VALUES (?, ?)"
    );

    if (!$stmtRelacion) {

        echo json_encode([
            "exito" => false,
            "mensaje" => "Error guardando las características."
        ]);

        exit;
    }

    foreach ($intolerancias as $idIntolerancia) {

        $idIntolerancia =
            intval($idIntolerancia);

        if ($idIntolerancia <= 0) {
            continue;
        }

        $stmtRelacion->bind_param(
            "ii",
            $id,
            $idIntolerancia
        );

        $stmtRelacion->execute();
    }

    $stmtRelacion->close();
}

// =========================
// RESPUESTA
// =========================

echo json_encode([
    "exito" => true,
    "mensaje" => "Lugar actualizado correctamente."
]);

$conexion->close();

exit;
?>