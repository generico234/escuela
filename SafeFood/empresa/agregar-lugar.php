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

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registrar local - SafeFood</title>

    <link
        rel="icon"
        href="../img/icono.png"
    >

    <link
        rel="stylesheet"
        href="../style.css"
    >

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
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

        <h1>Registrar un local</h1>

        <p>
            Completá los datos de tu establecimiento.
        </p>


        <label for="nombre">
            Nombre del local
        </label>

        <input
            type="text"
            id="nombre"
            placeholder="Ej: Café Miramar"
            required
        >


        <label for="horario">
            Horario
        </label>

        <input
            type="text"
            id="horario"
            placeholder="Ej: Lunes a viernes de 8:00 a 20:00"
            required
        >


        
<label for="descripcion">
    Descripción del local
</label>

<textarea
    id="descripcion"
    maxlength="1000"
    placeholder="Contá qué ofrece tu establecimiento..."
></textarea>


<hr>

<h3>Información de contacto</h3>

<label for="direccion">Dirección</label>
<input
    type="text"
    id="direccion"
    maxlength="255"
    placeholder="Ej: Calle 21 123, Miramar"
>

<label for="telefono">Teléfono</label>
<input
    type="tel"
    id="telefono"
    maxlength="30"
    placeholder="Ej: 2291 123456"
>

<label for="whatsapp">WhatsApp</label>
<input
    type="tel"
    id="whatsapp"
    maxlength="30"
    placeholder="Número con código de país y área"
>

<label for="instagram">Instagram u otra red social</label>
<input
    type="url"
    id="instagram"
    maxlength="255"
    placeholder="https://www.instagram.com/tu_local"
>


<hr>

<h3>Características del establecimiento</h3>

<label for="tipo_establecimiento">
    Tipo de establecimiento
</label>

<select id="tipo_establecimiento">
    <option value="">Seleccioná una opción</option>
    <option value="Restaurante">Restaurante</option>
    <option value="Cafetería">Cafetería</option>
    <option value="Bar">Bar</option>
    <option value="Panadería">Panadería</option>
    <option value="Pastelería">Pastelería</option>
    <option value="Heladería">Heladería</option>
    <option value="Dietética">Dietética</option>
    <option value="Rotisería">Rotisería</option>
    <option value="Comida rápida">Comida rápida</option>
    <option value="Otro">Otro</option>
</select>


<label for="rango_precios">Rango de precios</label>

<select id="rango_precios">
    <option value="">Sin especificar</option>
    <option value="$">$10000-$25000</option>
    <option value="$$">$30000-$60000</option>
    <option value="$$$">$70000-$150000</option>
</select>


<h3>Métodos de pago</h3>

<div class="opciones">

    <label>
        <input type="checkbox" name="metodos_pago" value="Efectivo">
        Efectivo
    </label>

    <label>
        <input type="checkbox" name="metodos_pago" value="Débito">
        Tarjeta de débito
    </label>

    <label>
        <input type="checkbox" name="metodos_pago" value="Crédito">
        Tarjeta de crédito
    </label>

    <label>
        <input type="checkbox" name="metodos_pago" value="Transferencia">
        Transferencia bancaria
    </label>

    <label>
        <input type="checkbox" name="metodos_pago" value="Billetera virtual">
        Billetera virtual
    </label>

</div>


<h3>Servicios</h3>

<div class="opciones">

    <label>
        <input type="checkbox" id="delivery">
        Ofrece delivery
    </label>

    <label>
        <input type="checkbox" id="take_away">
        Permite retirar en el local
    </label>

</div>


<label for="accesibilidad">
    Accesibilidad
</label>

<textarea
    id="accesibilidad"
    maxlength="1000"
    placeholder="Ej: Entrada accesible, baño adaptado, etc."
></textarea>


<label for="menu_url">Enlace al menú o carta</label>

<input
    type="url"
    id="menu_url"
    maxlength="500"
    placeholder="https://..."
>


        <h3>
            Opciones alimentarias
        </h3>

        <div class="opciones">

            <label>
                <input
                    type="checkbox"
                    value="1"
                >
                Sin gluten
            </label>

            <label>
                <input
                    type="checkbox"
                    value="2"
                >
                Sin lactosa
            </label>

            <label>
                <input
                    type="checkbox"
                    value="3"
                >
                Sin frutos secos
            </label>

            <label>
                <input
                    type="checkbox"
                    value="4"
                >
                Sin huevo
            </label>

            <label>
                <input
                    type="checkbox"
                    value="5"
                >
                Vegetariano
            </label>

            <label>
                <input
                    type="checkbox"
                    value="6"
                >
                Vegano
            </label>

        </div>


        <h3>
            Seleccioná la ubicación
        </h3>

        <p>
            Hacé clic en el mapa para colocar tu local.
        </p>

        <div
            id="mapaSeleccion"
            style="height: 400px;"
        ></div>


        <button
            type="button"
            id="guardarLugar"
        >
            Guardar local
        </button>


        <br><br>

        <a href="panel.php">
            Volver al panel
        </a>

    </section>

</main>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="agregar-lugar.js"></script>

</body>

</html>