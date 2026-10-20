
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

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "empresa"
) {
    responder(false, "No tenés permisos.");
}

require_once "../conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos)) {
    responder(false, "Los datos recibidos no son válidos.");
}

$idEmpresa = (int) $_SESSION["usuario_id"];
$idLugar = (int) ($datos["id_lugar"] ?? 0);

$nombre = trim($datos["nombre"] ?? "");
$horario = trim($datos["horario"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

$direccion = trim($datos["direccion"] ?? "");
$telefono = trim($datos["telefono"] ?? "");
$whatsapp = trim($datos["whatsapp"] ?? "");
$instagram = trim($datos["instagram"] ?? "");
$tipoEstablecimiento = trim($datos["tipo_establecimiento"] ?? "");
$rangoPrecios = trim($datos["rango_precios"] ?? "");
$accesibilidad = trim($datos["accesibilidad"] ?? "");
$menuUrl = trim($datos["menu_url"] ?? "");

$delivery = !empty($datos["delivery"]) ? 1 : 0;
$takeAway = !empty($datos["take_away"]) ? 1 : 0;

$intolerancias = $datos["intolerancias"] ?? [];
$metodosPagoRecibidos = $datos["metodos_pago"] ?? [];

if (
    $idLugar <= 0 ||
    $nombre === "" ||
    $horario === "" ||
    !is_array($intolerancias) ||
    !is_array($metodosPagoRecibidos)
) {
    responder(false, "Completá el nombre y el horario, y verificá los demás datos.");
}

// Validar enlaces solo si fueron completados.
foreach ([
    "Instagram" => $instagram,
    "Menú" => $menuUrl
] as $campo => $url) {
    if (
        $url !== "" &&
        filter_var($url, FILTER_VALIDATE_URL) === false
    ) {
        responder(false, "El enlace de " . $campo . " no tiene un formato válido.");
    }
}

// Aceptar únicamente métodos de pago permitidos.
$metodosPermitidos = [
    "Efectivo",
    "Débito",
    "Crédito",
    "Transferencia",
    "Billetera virtual"
];

$metodosPago = [];

foreach ($metodosPagoRecibidos as $metodo) {
    if (in_array($metodo, $metodosPermitidos, true)) {
        $metodosPago[] = $metodo;
    }
}

$metodosPago = array_values(array_unique($metodosPago));
$metodosPagoTexto = implode(", ", $metodosPago);

// Aceptar únicamente las seis opciones alimentarias conocidas.
$idsPermitidos = [1, 2, 3, 4, 5, 6];
$intoleranciasValidas = [];

foreach ($intolerancias as $id) {
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if ($id !== false && in_array($id, $idsPermitidos, true)) {
        $intoleranciasValidas[] = $id;
    }
}

$intoleranciasValidas = array_values(array_unique($intoleranciasValidas));

try {
    $conexion->begin_transaction();

    // Comprobar que el local pertenece a esta empresa.
    $sqlPropiedad = "
        SELECT id
        FROM lugares
        WHERE id = ? AND id_empresa = ?
        FOR UPDATE
    ";

    $stmt = $conexion->prepare($sqlPropiedad);
    $stmt->bind_param("ii", $idLugar, $idEmpresa);
    $stmt->execute();

    $propio = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$propio) {
        $conexion->rollback();
        responder(false, "No tenés permiso para editar este local o no existe.");
    }

    // Actualizar todos los datos del local.
    $sql = "
        UPDATE lugares
        SET
            nombre = ?,
            horario = ?,
            descripcion = ?,
            direccion = ?,
            telefono = ?,
            whatsapp = ?,
            instagram = ?,
            tipo_establecimiento = ?,
            rango_precios = ?,
            metodos_pago = ?,
            delivery = ?,
            take_away = ?,
            accesibilidad = ?,
            menu_url = ?
        WHERE id = ? AND id_empresa = ?
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "ssssssssssiissii",
        $nombre,
        $horario,
        $descripcion,
        $direccion,
        $telefono,
        $whatsapp,
        $instagram,
        $tipoEstablecimiento,
        $rangoPrecios,
        $metodosPagoTexto,
        $delivery,
        $takeAway,
        $accesibilidad,
        $menuUrl,
        $idLugar,
        $idEmpresa
    );

    $stmt->execute();
    $stmt->close();

    // Reemplazar las opciones alimentarias anteriores.
    $sqlEliminar = "
        DELETE FROM lugar_intolerancia
        WHERE id_lugar = ?
    ";

    $stmt = $conexion->prepare($sqlEliminar);
    $stmt->bind_param("i", $idLugar);
    $stmt->execute();
    $stmt->close();

    // Insertar las opciones seleccionadas actualmente.
    if (count($intoleranciasValidas) > 0) {
        $sqlInsertar = "
            INSERT INTO lugar_intolerancia
                (id_lugar, id_intolerancia)
            VALUES (?, ?)
        ";

        $stmt = $conexion->prepare($sqlInsertar);

        foreach ($intoleranciasValidas as $idIntolerancia) {
            $stmt->bind_param("ii", $idLugar, $idIntolerancia);
            $stmt->execute();
        }

        $stmt->close();
    }

    $conexion->commit();

    responder(true, "Local actualizado correctamente.");

} catch (Throwable $error) {
    try {
        $conexion->rollback();
    } catch (Throwable $errorRollback) {
        // La transacción podría no estar activa.
    }

    error_log("Error al actualizar local: " . $error->getMessage());

    responder(false, "No se pudieron guardar los cambios. Revisá los datos e intentá nuevamente.");
}
