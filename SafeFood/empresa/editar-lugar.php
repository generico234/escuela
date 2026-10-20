
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

$idEmpresa = (int) $_SESSION["usuario_id"];
$idLugar = (int) ($_GET["id"] ?? 0);

if ($idLugar <= 0) {
    header("Location: panel.php");
    exit;
}

// Buscar exclusivamente un local de esta empresa.
$sql = "SELECT *
        FROM lugares
        WHERE id = ? AND id_empresa = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $idLugar, $idEmpresa);
$stmt->execute();

$lugar = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lugar) {
    http_response_code(404);
    exit("No tenés permiso para editar este local o no existe.");
}

// Obtener opciones alimentarias seleccionadas.
$intoleranciasSeleccionadas = [];

$sql = "SELECT id_intolerancia
        FROM lugar_intolerancia
        WHERE id_lugar = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idLugar);
$stmt->execute();

$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $intoleranciasSeleccionadas[] =
        (int) $fila["id_intolerancia"];
}

$stmt->close();

// Obtener métodos de pago guardados.
$metodosPagoActuales = array_filter(
    array_map(
        "trim",
        explode(",", $lugar["metodos_pago"] ?? "")
    )
);

function h($valor) {
    return htmlspecialchars(
        (string) ($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$opciones = [
    1 => "Sin gluten",
    2 => "Sin lactosa",
    3 => "Sin frutos secos",
    4 => "Sin huevo",
    5 => "Vegetariano",
    6 => "Vegano"
];

$metodosPagoDisponibles = [
    "Efectivo",
    "Débito",
    "Crédito",
    "Transferencia",
    "Billetera virtual"
];

$tiposEstablecimiento = [
    "Restaurante",
    "Cafetería",
    "Panadería",
    "Heladería",
    "Rotisería",
    "Almacén",
    "Supermercado",
    "Dietética",
    "Bar",
    "Otro"
];

$rangoPrecios = [
    "$",
    "$$",
    "Hola ",
    "$$$$"
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar local - SafeFood</title>

    <link rel="icon" href="../img/icono.ico">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
<header class="barra">
    <div class="logo">
        <img src="../img/logo.jpeg" alt="SafeFood">
    </div>
</header>

<main class="pagina-agregar">
    <section class="formulario-lugar">
        <h1>Editar local</h1>

        <label for="nombre">Nombre del local *</label>
        <input
            type="text"
            id="nombre"
            maxlength="150"
            value="<?= h($lugar["nombre"]) ?>"
            required
        >

        <label for="horario">Horario *</label>
        <input
            type="text"
            id="horario"
            value="<?= h($lugar["horario"]) ?>"
            required
        >

        <label for="descripcion">Descripción del local</label>
        <textarea id="descripcion" maxlength="1000"><?= h($lugar["descripcion"]) ?></textarea>

        <label for="direccion">Dirección</label>
        <input type="text" id="direccion" value="<?= h($lugar["direccion"]) ?>">

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" value="<?= h($lugar["telefono"]) ?>">

        <label for="whatsapp">WhatsApp (opcional)</label>
        <input
            type="text"
            id="whatsapp"
            value="<?= h($lugar["whatsapp"]) ?>"
            placeholder="Número de contacto"
        >

        <label for="instagram">Instagram (opcional)</label>
        <input
            type="url"
            id="instagram"
            value="<?= h($lugar["instagram"]) ?>"
            placeholder="https://instagram.com/tu_local"
        >

        <label for="tipo_establecimiento">Tipo de establecimiento</label>
        <select id="tipo_establecimiento">
            <option value="">Seleccionar...</option>
            <?php foreach ($tiposEstablecimiento as $tipo): ?>
                <option
                    value="<?= h($tipo) ?>"
                    <?= ($lugar["tipo_establecimiento"] ?? "") === $tipo ? "selected" : "" ?>
                ><?= h($tipo) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="rango_precios">Rango de precios</label>
        <select id="rango_precios">
            <option value="">Sin especificar</option>
            <?php foreach ($rangoPrecios as $precio): ?>
                <option
                    value="<?= h($precio) ?>"
                    <?= ($lugar["rango_precios"] ?? "") === $precio ? "selected" : "" ?>
                ><?= h($precio) ?></option>
            <?php endforeach; ?>
        </select>

        <h3>Métodos de pago aceptados</h3>
        <div class="opciones">
            <?php foreach ($metodosPagoDisponibles as $metodo): ?>
                <label>
                    <input
                        type="checkbox"
                        class="metodo-pago"
                        value="<?= h($metodo) ?>"
                        <?= in_array($metodo, $metodosPagoActuales, true) ? "checked" : "" ?>
                    >
                    <?= h($metodo) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <h3>Servicios</h3>
        <div class="opciones">
            <label>
                <input
                    type="checkbox"
                    id="delivery"
                    <?= !empty($lugar["delivery"]) ? "checked" : "" ?>
                >
                Delivery
            </label>

            <label>
                <input
                    type="checkbox"
                    id="take_away"
                    <?= !empty($lugar["take_away"]) ? "checked" : "" ?>
                >
                Retiro por el local (take away)
            </label>
        </div>

        <label for="accesibilidad">Accesibilidad</label>
        <textarea id="accesibilidad" maxlength="2000"><?= h($lugar["accesibilidad"]) ?></textarea>

        <label for="menu_url">Enlace al menú (opcional)</label>
        <input
            type="url"
            id="menu_url"
            value="<?= h($lugar["menu_url"]) ?>"
            placeholder="https://..."
        >

        <h3>Opciones alimentarias</h3>
        <div class="opciones">
            <?php foreach ($opciones as $id => $nombreOpcion): ?>
                <label>
                    <input
                        type="checkbox"
                        class="intolerancia"
                        value="<?= $id ?>"
                        <?= in_array($id, $intoleranciasSeleccionadas, true) ? "checked" : "" ?>
                    >
                    <?= h($nombreOpcion) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="button" id="guardarCambios">
            Guardar cambios
        </button>

        <br><br>
        <a href="panel.php">Volver al panel</a>
    </section>
</main>

<script>
document.getElementById("guardarCambios").addEventListener("click", async function () {
    const valor = id => document.getElementById(id).value.trim();

    const nombre = valor("nombre");
    const horario = valor("horario");

    if (!nombre) {
        alert("Ingresá el nombre del local.");
        return;
    }

    if (!horario) {
        alert("Ingresá el horario.");
        return;
    }

    const intolerancias = Array.from(
        document.querySelectorAll(".intolerancia:checked")
    ).map(c => Number(c.value));

    const metodos_pago = Array.from(
        document.querySelectorAll(".metodo-pago:checked")
    ).map(c => c.value);

    const datos = {
        id_lugar: <?= $idLugar ?>,
        nombre,
        horario,
        descripcion: valor("descripcion"),
        direccion: valor("direccion"),
        telefono: valor("telefono"),
        whatsapp: valor("whatsapp"),
        instagram: valor("instagram"),
        tipo_establecimiento: valor("tipo_establecimiento"),
        rango_precios: valor("rango_precios"),
        metodos_pago,
        delivery: document.getElementById("delivery").checked,
        take_away: document.getElementById("take_away").checked,
        accesibilidad: valor("accesibilidad"),
        menu_url: valor("menu_url"),
        intolerancias
    };

    try {
        const respuesta = await fetch("actualizar-lugar.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        const texto = await respuesta.text();
        let resultado;

        try {
            resultado = JSON.parse(texto);
        } catch {
            console.error("Respuesta del servidor:", texto);
            alert("El servidor devolvió una respuesta inesperada. Revisá la consola.");
            return;
        }

        if (!respuesta.ok || !resultado.exito) {
            alert(resultado.mensaje || "No se pudieron guardar los cambios.");
            return;
        }

        alert("Local actualizado correctamente.");
        window.location.href = "panel.php";

    } catch (error) {
        console.error("Error actualizando:", error);
        alert("No se pudo conectar con el servidor.");
    }
});
</script>

</body>
</html>
