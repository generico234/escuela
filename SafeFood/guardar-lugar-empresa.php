
<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


// =========================
// COMPROBAR SESIÓN DE EMPRESA
// =========================

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "empresa"
) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "No tenés permisos para registrar locales."
    ]);
    exit;
}


// =========================
// CONEXIÓN
// =========================

require_once "conexion.php";


// =========================
// RECIBIR DATOS
// =========================

$datos = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($datos)) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "No se recibieron datos válidos."
    ]);
    exit;
}


// =========================
// DATOS DEL LOCAL
// =========================

$nombre = trim($datos["nombre"] ?? "");
$horario = trim($datos["horario"] ?? "");
$descripcion = trim($datos["descripcion"] ?? "");

$direccion = trim($datos["direccion"] ?? "");
$telefono = trim($datos["telefono"] ?? "");
$whatsapp = trim($datos["whatsapp"] ?? "");
$instagram = trim($datos["instagram"] ?? "");

$tipoEstablecimiento = trim(
    $datos["tipo_establecimiento"] ?? ""
);

$rangoPrecios = trim(
    $datos["rango_precios"] ?? ""
);

$accesibilidad = trim(
    $datos["accesibilidad"] ?? ""
);

$menuUrl = trim(
    $datos["menu_url"] ?? ""
);


// =========================
// VALIDAR MÉTODOS DE PAGO
// =========================

$metodosPermitidos = [
    "Efectivo",
    "Débito",
    "Crédito",
    "Transferencia",
    "Billetera virtual"
];

$metodosRecibidos = $datos["metodos_pago"] ?? [];
$metodosPago = [];

if (is_array($metodosRecibidos)) {
    foreach ($metodosRecibidos as $metodo) {
        if (
            is_string($metodo) &&
            in_array($metodo, $metodosPermitidos, true)
        ) {
            $metodosPago[] = $metodo;
        }
    }
}

$metodosPago = array_unique($metodosPago);
$metodosPagoTexto = implode(", ", $metodosPago);


// =========================
// SERVICIOS
// =========================

$delivery = !empty($datos["delivery"]) ? 1 : 0;
$takeAway = !empty($datos["take_away"]) ? 1 : 0;


// =========================
// UBICACIÓN
// =========================

$latitud = filter_var(
    $datos["latitud"] ?? null,
    FILTER_VALIDATE_FLOAT
);

$longitud = filter_var(
    $datos["longitud"] ?? null,
    FILTER_VALIDATE_FLOAT
);


// =========================
// INTOLERANCIAS
// =========================

$intolerancias = $datos["intolerancias"] ?? [];

$intoleranciasPermitidas = [1, 2, 3, 4, 5, 6];

if (!is_array($intolerancias)) {
    $intolerancias = [];
}

$intolerancias = array_values(
    array_unique(
        array_filter(
            array_map("intval", $intolerancias),
            function ($id) use ($intoleranciasPermitidas) {
                return in_array($id, $intoleranciasPermitidas, true);
            }
        )
    )
);


// =========================
// VALIDACIONES
// =========================

if ($nombre === "" || $horario === "") {
    echo json_encode([
        "exito" => false,
        "mensaje" => "El nombre y el horario son obligatorios."
    ]);
    exit;
}

if (
    $latitud === false ||
    $longitud === false ||
    $latitud < -90 ||
    $latitud > 90 ||
    $longitud < -180 ||
    $longitud > 180
) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "La ubicación seleccionada no es válida."
    ]);
    exit;
}

if (
    strlen($nombre) > 255 ||
    strlen($horario) > 255 ||
    strlen($direccion) > 255 ||
    strlen($telefono) > 30 ||
    strlen($whatsapp) > 30 ||
    strlen($instagram) > 255 ||
    strlen($tipoEstablecimiento) > 80 ||
    strlen($rangoPrecios) > 30 ||
    strlen($menuUrl) > 500
) {
    echo json_encode([
        "exito" => false,
        "mensaje" => "Uno de los campos supera la longitud permitida."
    ]);
    exit;
}

foreach ([$instagram, $menuUrl] as $url) {
    if ($url !== "" && !filter_var($url, FILTER_VALIDATE_URL)) {
        echo json_encode([
            "exito" => false,
            "mensaje" => "Instagram y el menú deben contener una URL válida."
        ]);
        exit;
    }
}



 // =========================
 // OBTENER EL ID REAL DE LA EMPRESA
 // =========================

$idUsuario = intval($_SESSION["usuario_id"]);

$stmtEmpresa = $conexion->prepare(
    "SELECT id FROM empresas WHERE id_usuario = ?"
);

if (!$stmtEmpresa) {
    http_response_code(500);
    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo consultar la empresa."
    ]);
    exit;
}

$stmtEmpresa->bind_param("i", $idUsuario);
$stmtEmpresa->execute();
$stmtEmpresa->bind_result($idEmpresa);

if (!$stmtEmpresa->fetch()) {
    $stmtEmpresa->close();

    echo json_encode([
        "exito" => false,
        "mensaje" => "Tu usuario no tiene una empresa registrada."
    ]);
    exit;
}

$stmtEmpresa->close();



// =========================
// GUARDAR LOCAL E INTOLERANCIAS
// EN UNA TRANSACCIÓN
// =========================

$conexion->begin_transaction();

try {

    $sql = "
        INSERT INTO lugares (
            id_empresa,
            nombre,
            latitud,
            longitud,
            horario,
            descripcion,
            direccion,
            telefono,
            whatsapp,
            instagram,
            tipo_establecimiento,
            rango_precios,
            metodos_pago,
            delivery,
            take_away,
            accesibilidad,
            menu_url
        )
        VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        throw new Exception(
            "Error preparando el registro: " . $conexion->error
        );
    }

$stmt->bind_param(
    "isddsssssssssisss",
    $idEmpresa,
    $nombre,
    $latitud,
    $longitud,
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
    $menuUrl
);

    if (!$stmt->execute()) {
        throw new Exception(
            "Error guardando el local: " . $stmt->error
        );
    }

    $idLugar = $conexion->insert_id;

    $stmt->close();


    // =========================
    // GUARDAR INTOLERANCIAS
    // =========================

    if (count($intolerancias) > 0) {

        $sqlIntolerancia = "
            INSERT INTO lugar_intolerancia (
                id_lugar,
                id_intolerancia
            )
            VALUES (?, ?)
        ";

        $stmtIntolerancia = $conexion->prepare(
            $sqlIntolerancia
        );

        if (!$stmtIntolerancia) {
            throw new Exception("No se pudieron preparar las opciones alimentarias.");
        }

        foreach ($intolerancias as $idIntolerancia) {

            $stmtIntolerancia->bind_param(
                "ii",
                $idLugar,
                $idIntolerancia
            );

            if (!$stmtIntolerancia->execute()) {
                throw new Exception("No se pudieron guardar las opciones alimentarias.");
            }
        }

        $stmtIntolerancia->close();
    }


    $conexion->commit();

    echo json_encode([
        "exito" => true,
        "mensaje" => "Local registrado correctamente.",
        "id" => $idLugar
    ]);

    exit;

}  catch (Throwable $error) {

    $conexion->rollback();

    error_log(
        "Error al registrar local de empresa: " .
        $error->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudo guardar el local. Revisá los datos e intentá nuevamente."
    ]);

    exit;
}


