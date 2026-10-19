
<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

function responder($exito, $mensaje, $datosExtra = [], $codigoHttp = 200)
{
    http_response_code($codigoHttp);

    echo json_encode(
        array_merge([
            "exito" => $exito,
            "mensaje" => $mensaje
        ], $datosExtra),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

// =========================
// COMPROBAR SESIÓN DE ADMIN
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    responder(
        false,
        "No tenés permisos para realizar esta acción.",
        [],
        403
    );
}

// =========================
// RECIBIR Y VALIDAR DATOS
// =========================

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($datos)) {
    responder(false, "Los datos recibidos no son válidos.", [], 400);
}

$nombre = trim($datos["nombre"] ?? "");
$horario = trim($datos["horario"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

$latitudRecibida = $datos["latitud"] ?? null;
$longitudRecibida = $datos["longitud"] ?? null;

$intolerancias = $datos["intolerancias"] ?? [];

if ($nombre === "" || $horario === "") {
    responder(
        false,
        "El nombre y el horario son obligatorios.",
        [],
        400
    );
}

if (
    !is_string($nombre) ||
    !is_string($horario) ||
    !is_string($descripcion)
) {
    responder(false, "El formato de los campos no es válido.", [], 400);
}

if (
    mb_strlen($nombre, "UTF-8") > 255 ||
    mb_strlen($horario, "UTF-8") > 255 ||
    mb_strlen($descripcion, "UTF-8") > 1000
) {
    responder(
        false,
        "Uno de los campos supera la longitud permitida.",
        [],
        400
    );
}

if (
    !is_numeric($latitudRecibida) ||
    !is_numeric($longitudRecibida)
) {
    responder(false, "La ubicación seleccionada no es válida.", [], 400);
}

$latitud = (float) $latitudRecibida;
$longitud = (float) $longitudRecibida;

if (
    !is_finite($latitud) ||
    !is_finite($longitud) ||
    $latitud < -90 ||
    $latitud > 90 ||
    $longitud < -180 ||
    $longitud > 180
) {
    responder(
        false,
        "Las coordenadas están fuera del rango permitido.",
        [],
        400
    );
}

if (!is_array($intolerancias)) {
    responder(
        false,
        "Las opciones alimentarias no tienen un formato válido.",
        [],
        400
    );
}

// Solo se permiten los seis IDs existentes en SafeFood.
$idsPermitidos = [1, 2, 3, 4, 5, 6];
$intoleranciasValidadas = [];

foreach ($intolerancias as $id) {
    $idValidado = filter_var($id, FILTER_VALIDATE_INT);

    if (
        $idValidado === false ||
        !in_array($idValidado, $idsPermitidos, true)
    ) {
        responder(
            false,
            "Una de las opciones alimentarias no es válida.",
            [],
            400
        );
    }

    $intoleranciasValidadas[] = $idValidado;
}

$intoleranciasValidadas = array_values(
    array_unique($intoleranciasValidadas)
);

// =========================
// CONEXIÓN Y TRANSACCIÓN
// =========================

require_once "conexion.php";

try {
    $conexion->begin_transaction();

    // Crear el lugar.
    $sql = "
        INSERT INTO lugares
            (nombre, latitud, longitud, horario, descripcion)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException("No se pudo preparar el alta del lugar.");
    }

    $stmt->bind_param(
        "sddss",
        $nombre,
        $latitud,
        $longitud,
        $horario,
        $descripcion
    );

    if (!$stmt->execute()) {
        throw new RuntimeException("No se pudo insertar el lugar.");
    }

    $idLugar = (int) $conexion->insert_id;
    $stmt->close();

    // Guardar las opciones alimentarias seleccionadas.
    if (count($intoleranciasValidadas) > 0) {
        $sqlRelacion = "
            INSERT INTO lugar_intolerancia
                (id_lugar, id_intolerancia)
            VALUES (?, ?)
        ";

        $stmtRelacion = $conexion->prepare($sqlRelacion);

        if (!$stmtRelacion) {
            throw new RuntimeException(
                "No se pudo preparar el guardado de las opciones."
            );
        }

        foreach ($intoleranciasValidadas as $idIntolerancia) {
            $stmtRelacion->bind_param(
                "ii",
                $idLugar,
                $idIntolerancia
            );

            if (!$stmtRelacion->execute()) {
                throw new RuntimeException(
                    "No se pudieron guardar todas las opciones alimentarias."
                );
            }
        }

        $stmtRelacion->close();
    }

    // Confirmar ambas operaciones juntas.
    $conexion->commit();
    $conexion->close();

    responder(
        true,
        "Lugar guardado correctamente.",
        ["id" => $idLugar]
    );

} catch (Throwable $error) {
    try {
        $conexion->rollback();
    } catch (Throwable $errorRollback) {
        // La transacción podría haber terminado.
    }

    error_log(
        "Error al guardar lugar desde administración: " .
        $error->getMessage()
    );

    if (isset($conexion)) {
        $conexion->close();
    }

    responder(
        false,
        "No se pudo guardar el lugar. Revisá los datos e intentá nuevamente.",
        [],
        500
    );
}
