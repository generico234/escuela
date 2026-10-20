<?php

session_start();

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "empresa"
) {
    header("Location: ../login.php");
    exit;
}

require_once "../conexion.php";


$idUsuario = intval($_SESSION["usuario_id"]);
$usuario = $_SESSION["usuario"] ?? "Empresa";

// Obtener el ID real de la empresa
$sqlEmpresa = "
    SELECT id
    FROM empresas
    WHERE id_usuario = ?
";

$stmtEmpresa = $conexion->prepare($sqlEmpresa);
$stmtEmpresa->bind_param("i", $idUsuario);
$stmtEmpresa->execute();
$stmtEmpresa->bind_result($idEmpresa);

if (!$stmtEmpresa->fetch()) {
    $stmtEmpresa->close();

    exit("Error: tu usuario no tiene una empresa registrada.");
}

$stmtEmpresa->close();



// =========================
// OBTENER LOCALES DE LA EMPRESA
// =========================

$sql = "
    SELECT
        id,
        nombre,
        horario,
        descripcion,
        latitud,
        longitud
    FROM lugares
    WHERE id_empresa = ?
    ORDER BY id DESC
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $idEmpresa
);

$stmt->execute();

$resultado = $stmt->get_result();

$locales = [];

while ($fila = $resultado->fetch_assoc()) {

    $locales[] = $fila;

}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel de empresa - SafeFood</title>

    <link
        rel="icon"
        href="../img/icono.png"
    >

    <link
        rel="stylesheet"
        href="../style.css"
    >

</head>

<body>

<header class="barra">

    <div class="logo">

        <img
            src="../img/logo.jpeg"
            alt="SafeFood"
        >

    </div>

</header>


<main class="pagina-agregar">

    <section class="formulario-lugar">

        <h1>
            Panel de empresa
        </h1>

        <p>
            Bienvenido/a,
            <strong>
                <?= htmlspecialchars($usuario) ?>
            </strong>.
        </p>



        <a
            href="agregar-lugar.php"
            class="boton-guardar"
        >
            + Registrar un local
        </a>



        <hr>


        <h2>
            Mis locales
        </h2>


        <?php if (count($locales) === 0): ?>

            <div class="mensaje-comentario">

                <strong>
                    Todavía no registraste ningún local.
                </strong>

                <p>
                    Utilizá el botón de arriba para registrar
                    tu primer establecimiento.
                </p>

            </div>

        <?php else: ?>


            <div class="lista-locales-empresa">

                <?php foreach ($locales as $local): ?>

                    <article class="tarjeta-local-empresa">

                        <h3>
                            <?= htmlspecialchars($local["nombre"]) ?>
                        </h3>


                        <p>
                            <strong>Horario:</strong>
                            <?= htmlspecialchars($local["horario"]) ?>
                        </p>


                        <?php if (!empty($local["descripcion"])): ?>

                            <p>
                                <?= htmlspecialchars(
                                    $local["descripcion"]
                                ) ?>
                            </p>

                        <?php endif; ?>


                        <div class="acciones-local-empresa">

                            <a
                                href="editar-lugar.php?id=<?= intval($local["id"]) ?>"
                                class="boton-sesion boton-panel"
                            >
                                Editar
                            </a>


<button
    type="button"
    class="boton-eliminar-local-empresa"
    onclick="eliminarLocal(<?= (int) $local['id'] ?>)"
>
    Eliminar
</button>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>


        <br>


        <a href="../index.html">
            Volver al mapa
        </a>

    </section>

</main>


<script>

async function eliminarLocal(idLocal) {

    const confirmar = confirm(
        "¿Querés eliminar este local?"
    );

    if (!confirmar) {
        return;
    }


    try {

        const respuesta = await fetch(
            "eliminar-lugar.php",
            {
                method: "POST",

                headers: {
                    "Content-Type":
                        "application/json"
                },

                body: JSON.stringify({
                    id_lugar: idLocal
                })
            }
        );


        const texto =
            await respuesta.text();


        console.log(
            "Respuesta eliminar:",
            texto
        );


        let datos;


        try {

            datos =
                JSON.parse(texto);

        } catch (error) {

            console.error(
                "Respuesta no válida:",
                texto
            );

            alert(
                "El servidor devolvió una respuesta inesperada."
            );

            return;
        }


        if (!datos.exito) {

            alert(
                datos.mensaje
            );

            return;
        }


        location.reload();


    } catch (error) {

        console.error(
            "Error eliminando local:",
            error
        );

        alert(
            "No se pudo eliminar el local."
        );

    }

}

</script>

</body>

</html>