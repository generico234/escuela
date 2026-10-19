<?php
session_start();

if (
    !isset($_SESSION["usuario_id"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] !== "admin"
) {
    header("Location: ../login.php");
    exit;
}

$conexion = new mysqli("localhost", "root", "", "safefood");

if ($conexion->connect_error) {
    die("Error de conexión con MySQL");
}

$conexion->set_charset("utf8mb4");

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Lugar no válido");
}

/* Obtener datos del lugar */
$stmt = $conexion->prepare(
    "SELECT id, nombre, latitud, longitud, horario, descripcion
     FROM lugares
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$lugar = $resultado->fetch_assoc();

if (!$lugar) {
    die("El lugar no existe");
}

/* Obtener intolerancias del lugar */
$intoleranciasSeleccionadas = [];

$stmtInt = $conexion->prepare(
    "SELECT id_intolerancia
     FROM lugar_intolerancia
     WHERE id_lugar = ?"
);

$stmtInt->bind_param("i", $id);
$stmtInt->execute();

$resultadoInt = $stmtInt->get_result();

while ($fila = $resultadoInt->fetch_assoc()) {
    $intoleranciasSeleccionadas[] = $fila["id_intolerancia"];
}

/* Obtener todas las intolerancias */
$resultadoTodas = $conexion->query(
    "SELECT id, nombre
     FROM intolerancias
     ORDER BY id ASC"
);

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar lugar - SafeFood</title>

    <link rel="icon" href="../img/icono.png">

    <link rel="stylesheet" href="../style.css">

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

</head>

<body>

<header class="barra">

    <div class="logo">
        <img src="../img/logo.jpeg" alt="SafeFood">
    </div>

    <a href="panel.php" class="volver">
         Volver al panel
    </a>

</header>


<main class="pagina-agregar">

    <section class="formulario-lugar">

        <h1> Editar lugar</h1>

        <p>
            Modificá los datos de
            <strong><?= htmlspecialchars($lugar["nombre"]) ?></strong>
        </p>

        <form id="formularioEditar">

            <input
                type="hidden"
                id="id"
                value="<?= $lugar["id"] ?>"
            >

            <label for="nombre">
                 Nombre del lugar
            </label>

            <input
                type="text"
                id="nombre"
                value="<?= htmlspecialchars($lugar["nombre"]) ?>"
                required
            >


            <label for="horario">
                 Horario
            </label>

            <input
                type="text"
                id="horario"
                value="<?= htmlspecialchars($lugar["horario"]) ?>"
                required
            >

            <label for="descripcion">
                 Descripción del local
            </label>

            <textarea
                id="descripcion"
                maxlength="1000"
                placeholder="Describí brevemente el local para ayudar a reconocerlo..."
            ><?= htmlspecialchars($lugar["descripcion"] ?? "") ?></textarea>


            <h3> Intolerancias / características</h3>

            <div class="intolerancias-form">

                <?php while ($intolerancia = $resultadoTodas->fetch_assoc()): ?>

                    <label>

                        <input
                            type="checkbox"
                            name="intolerancias"
                            value="<?= $intolerancia["id"] ?>"

                            <?= in_array(
                                $intolerancia["id"],
                                $intoleranciasSeleccionadas
                            ) ? "checked" : "" ?>
                        >

                        <?= htmlspecialchars($intolerancia["nombre"]) ?>

                    </label>

                <?php endwhile; ?>

            </div>


            <h3> Ubicación</h3>

            <p>
                Hacé clic en el mapa para cambiar la ubicación.
            </p>

            <div id="mapaEditar"></div>

            <p id="coordenadasEditar"></p>


            <button
                type="submit"
                class="boton-guardar"
            >
                 Guardar cambios
            </button>

        </form>

        <p id="mensaje"></p>

    </section>

</main>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

const idLugar = <?= $lugar["id"] ?>;

let latitud = <?= $lugar["latitud"] ?>;
let longitud = <?= $lugar["longitud"] ?>;


/* MAPA */

const mapa = L.map("mapaEditar").setView(
    [latitud, longitud],
    16
);


L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors"
    }
).addTo(mapa);


/* MARCADOR */

let marcador = L.marker(
    [latitud, longitud]
).addTo(mapa);


/* CAMBIAR UBICACIÓN */

mapa.on("click", function(evento) {

    latitud = evento.latlng.lat;
    longitud = evento.latlng.lng;

    marcador.setLatLng([
        latitud,
        longitud
    ]);

    document.getElementById("coordenadasEditar").textContent =
        "📍 " +
        latitud.toFixed(7) +
        ", " +
        longitud.toFixed(7);

});


/* COORDENADAS INICIALES */

document.getElementById("coordenadasEditar").textContent =
    "📍 " +
    latitud.toFixed(7) +
    ", " +
    longitud.toFixed(7);


/* FORMULARIO */

document
    .getElementById("formularioEditar")
    .addEventListener("submit", async function(evento) {

        evento.preventDefault();


        const nombre =
            document.getElementById("nombre").value.trim();

        const horario =
            document.getElementById("horario").value.trim();

        const descripcion =
                document.getElementById("descripcion").value.trim();

        const intolerancias = Array.from(
            document.querySelectorAll(
                'input[name="intolerancias"]:checked'
            )
        ).map(function(checkbox) {

            return checkbox.value;

        });


        const mensaje =
            document.getElementById("mensaje");


        try {

            const respuesta = await fetch(
                "actualizar-lugar.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({

                        id: idLugar,

                        nombre: nombre,

                        horario: horario,

                        descripcion: descripcion,

                        latitud: latitud,

                        longitud: longitud,

                        intolerancias: intolerancias

                    })
                }
            );


            const resultado =
                await respuesta.json();


            if (resultado.exito) {

                mensaje.textContent =
                    "✅ Lugar actualizado correctamente :v";

                setTimeout(function() {

                    window.location.href =
                        "panel.php";

                }, 1000);

            } else {

                mensaje.textContent =
                    "❌ " + resultado.mensaje;

            }


        } catch (error) {

            console.error(error);

            mensaje.textContent =
                "❌ Error al conectar con el servidor.";

        }

    });

</script>

</body>

</html>