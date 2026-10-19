
<?php

header("Content-Type: application/json; charset=UTF-8");

// =========================
// CONEXIÓN A MYSQL
// =========================

require_once "conexion.php";

// =========================
// OBTENER LUGARES
// =========================

$sql = "
    SELECT
        l.id,
        l.nombre,
        l.latitud,
        l.longitud,
        l.horario,
        l.descripcion,
        l.direccion,
        l.telefono,
        l.whatsapp,
        l.instagram,
        l.tipo_establecimiento,
        l.rango_precios,
        l.metodos_pago,
        l.delivery,
        l.take_away,
        l.accesibilidad,
        l.menu_url,
        GROUP_CONCAT(
            DISTINCT i.nombre
            ORDER BY i.nombre
            SEPARATOR ','
        ) AS intolerancias

    FROM lugares l

    LEFT JOIN lugar_intolerancia li
        ON l.id = li.id_lugar

    LEFT JOIN intolerancias i
        ON li.id_intolerancia = i.id

    GROUP BY
        l.id,
        l.nombre,
        l.latitud,
        l.longitud,
        l.horario,
        l.descripcion,
        l.direccion,
        l.telefono,
        l.whatsapp,
        l.instagram,
        l.tipo_establecimiento,
        l.rango_precios,
        l.metodos_pago,
        l.delivery,
        l.take_away,
        l.accesibilidad,
        l.menu_url
";

$resultado = $conexion->query($sql);

if (!$resultado) {
    error_log(
        "Error al consultar lugares: " .
        $conexion->error
    );

    http_response_code(500);

    echo json_encode([
        "exito" => false,
        "mensaje" => "No se pudieron consultar los lugares."
    ]);

    $conexion->close();
    exit;
}

// =========================
// CREAR LISTA DE LUGARES
// =========================

$lugares = [];

while ($fila = $resultado->fetch_assoc()) {

    $intolerancias = [];

    if (
        $fila["intolerancias"] !== null &&
        $fila["intolerancias"] !== ""
    ) {
        $intolerancias = explode(
            ",",
            $fila["intolerancias"]
        );
    }

    $lugares[] = [
        "id" => intval($fila["id"]),
        "nombre" => $fila["nombre"],
        "lat" => floatval($fila["latitud"]),
        "lng" => floatval($fila["longitud"]),
        "horario" => $fila["horario"] ?? "",
        "descripcion" => $fila["descripcion"] ?? "",
        "direccion" => $fila["direccion"] ?? "",
        "telefono" => $fila["telefono"] ?? "",
        "whatsapp" => $fila["whatsapp"] ?? "",
        "instagram" => $fila["instagram"] ?? "",
        "tipo_establecimiento" =>
            $fila["tipo_establecimiento"] ?? "",
        "rango_precios" => $fila["rango_precios"] ?? "",
        "metodos_pago" => $fila["metodos_pago"] ?? "",
        "delivery" => intval($fila["delivery"] ?? 0),
        "take_away" => intval($fila["take_away"] ?? 0),
        "accesibilidad" => $fila["accesibilidad"] ?? "",
        "menu_url" => $fila["menu_url"] ?? "",
        "intolerancias" => $intolerancias
    ];
}

// =========================
// DEVOLVER JSON
// =========================

echo json_encode(
    [
        "exito" => true,
        "lugares" => $lugares
    ],
    JSON_UNESCAPED_UNICODE
);

$conexion->close();

?>
