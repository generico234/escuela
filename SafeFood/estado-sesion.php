<?php

session_start();

header("Content-Type: application/json; charset=utf-8");

if (isset($_SESSION["usuario_id"])) {

    echo json_encode([
        "logueado" => true,
        "usuario" => $_SESSION["usuario"],
        "id" => $_SESSION["usuario_id"],
        "rol" => $_SESSION["rol"] ?? "usuario"
    ]);

} else {

    echo json_encode([
        "logueado" => false
    ]);
}

exit;