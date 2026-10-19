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

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar lugar - SafeFood</title>

    <link rel="icon" href="../img/icono.ico">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <header class="barra">

        <div class="logo">
            <img src="../img/logo.jpeg" alt="SafeFood">
        </div>

        <a href="../index.html" class="volver">
            ← Volver al mapa
        </a>

    </header>


    <main class="pagina-agregar">

        <section class="formulario-lugar">

            <h1> Agregar lugar</h1>

            <p>
                Completá la información del establecimiento.
            </p>


            <form id="formularioLugar">

                <!-- NOMBRE -->

                <div class="campo">

                    <label for="nombre">
                        Nombre del lugar
                    </label>

                    <input type="text" id="nombre" placeholder="Ej: Restaurante Verde" required>

                </div>


                <!-- HORARIO -->

                <div class="campo">

                    <label for="horario">
                        Horario
                    </label>

                    <input type="text" id="horario" placeholder="Ej: 12:00 - 23:00" required>

                </div>

                <div class="campo">
                    <label for="descripcion">Descripción del local</label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        placeholder="Describí brevemente el local para ayudar a reconocerlo..."
                        maxlength="1000"
                    ></textarea>
                    
                </div>

                <div class="campo">

                    <label>
                        📍 Ubicación del lugar
                    </label>

                    <p class="ayuda-mapa">
                        Hacé clic en el mapa donde se encuentra el establecimiento.
                    </p>

                    <div id="mapaSeleccion"></div>

                    <p id="coordenadas">
                        📍 Todavía no seleccionaste una ubicación.
                    </p>

                </div>


                <!-- INTOLERANCIAS -->

                <div class="campo">

                    <label>
                        Intolerancias aptas
                    </label>


                    <div class="checkboxes">

                        <label>
                            <input type="checkbox" name="intolerancias" value="1">
                             Gluten
                        </label>


                        <label>
                            <input type="checkbox" name="intolerancias" value="2">
                             Lactosa
                        </label>


                        <label>
                            <input type="checkbox" name="intolerancias" value="3">
                             Frutos secos
                        </label>


                        <label>
                            <input type="checkbox" name="intolerancias" value="4">
                             Huevo
                        </label>


                        <label>
                            <input type="checkbox" name="intolerancias" value="5">
                            Vegetariano
                        </label>

                        <label>
                            <input type="checkbox" name="intolerancias" value="6">
                            Vegano
                        </label>                  

                    </div>

                </div>


                <button type="submit" class="boton-guardar">
                     Guardar lugar
                </button>

            </form>


            <p id="mensaje"></p>

        </section>

    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>

    <script src="agregar-lugar.js"></script>

</body>

</html>