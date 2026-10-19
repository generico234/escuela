<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";


$idLugar = intval(
    $_GET["id_lugar"] ?? 0
);


if ($idLugar <= 0) {

    echo json_encode([
        "exito" => false,
        "mensaje" => "Lugar inválido."
    ]);

    exit;
}


$sql = "
    SELECT
        c.id,
        c.comentario,
        c.creado_en,
        u.usuario

    FROM comentarios c

    INNER JOIN usuarios u
        ON c.id_usuario = u.id

    WHERE c.id_lugar = ?

    ORDER BY c.creado_en DESC
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
    $idLugar
);

$stmt->execute();


$resultado = $stmt->get_result();


$comentarios = [];


while ($fila = $resultado->fetch_assoc()) {

    $comentarios[] = [

        "id" => intval($fila["id"]),

        "usuario" => $fila["usuario"],

        "comentario" => $fila["comentario"],

        "creado_en" => $fila["creado_en"]

    ];
}


echo json_encode([
    "exito" => true,
    "comentarios" => $comentarios
]);


$stmt->close();
$conexion->close();

exit;

?>