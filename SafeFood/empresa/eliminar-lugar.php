
<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

function responder($exito, $mensaje) {
    echo json_encode([
        "exito" => $exito,
        "mensaje" => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// =========================
// VALIDAR SESIÓN
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "empresa"
) {
    http_response_code(403);
    responder(false, "No tenés permisos para eliminar locales.");
}

// =========================
// CONEXIÓN Y DATOS
// =========================

require_once "../conexion.php";

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($datos)) {
    http_response_code(400);
    responder(false, "Los datos recibidos no son válidos.");
}

$idLugar = filter_var(
    $datos["id_lugar"] ?? null,
    FILTER_VALIDATE_INT
);

$idEmpresa = (int) $_SESSION["usuario_id"];

if ($idLugar === false || $idLugar === null || $idLugar <= 0) {
    http_response_code(400);
    responder(false, "El ID del local no es válido.");
}

// =========================
// ELIMINAR LOCAL PROPIO
// =========================

try {
    $conexion->begin_transaction();

    // Bloquear y comprobar que el local pertenece
    // a la empresa que inició sesión.
    $sql = "
        SELECT id
        FROM lugares
        WHERE id = ? AND id_empresa = ?
        FOR UPDATE
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $idLugar, $idEmpresa);
    $stmt->execute();

    $local = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$local) {
        $conexion->rollback();
        http_response_code(404);
        responder(false, "El local no existe o no te pertenece.");
    }

    // Eliminar las opciones alimentarias asociadas.
    $sql = "
        DELETE FROM lugar_intolerancia
        WHERE id_lugar = ?
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idLugar);
    $stmt->execute();
    $stmt->close();

    // Eliminar el local únicamente si sigue perteneciendo
    // a la empresa autenticada.
    $sql = "
        DELETE FROM lugares
        WHERE id = ? AND id_empresa = ?
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $idLugar, $idEmpresa);
    $stmt->execute();

    $eliminado = $stmt->affected_rows;
    $stmt->close();

    if ($eliminado !== 1) {
        throw new RuntimeException(
            "No se pudo confirmar la eliminación del local."
        );
    }

    $conexion->commit();
    $conexion->close();

    responder(true, "Local eliminado correctamente.");

} catch (Throwable $error) {
    try {
        $conexion->rollback();
    } catch (Throwable $errorRollback) {
        // Puede que la transacción ya no esté activa.
    }

    error_log(
        "Error al eliminar local de empresa: " .
        $error->getMessage()
    );

    if (isset($conexion)) {
        $conexion->close();
    }

    http_response_code(500);
    responder(
        false,
        "No se pudo eliminar el local. Revisá los datos e intentá nuevamente."
    );
}
