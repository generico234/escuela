<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión - SafeFood</title>

    <link rel="icon" href="img/icono.png">
    <link rel="stylesheet" href="style.css">

</head>

<body>

<header class="barra">

    <div class="logo">
        <img src="img/logo.jpeg" alt="SafeFood">
    </div>

</header>


<main class="pagina-agregar">

    <section class="formulario-lugar">

        <h1>🔐 Iniciar sesión</h1>

        <p>
            Iniciá sesión para acceder a tu cuenta de SafeFood.
        </p>


                <form action="verificar-login.php" method="POST">

            <label for="usuario">
                Usuario
            </label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Ingresá tu usuario"
                required
            >

            <label for="contrasena">
                Contraseña
            </label>

            <input
                type="password"
                id="contrasena"
                name="contrasena"
                placeholder="Ingresá tu contraseña"
                required
            >

            <button type="submit">
                Iniciar sesión
            </button>

        </form>
        <?php

        if (isset($_GET["registro"]) && $_GET["registro"] === "ok") {
            echo "<p>Cuenta creada correctamente. Ahora podés iniciar sesión.</p>";
        }

        if (isset($_GET["error"])) {
            echo "<p>Usuario o contraseña incorrectos.</p>";
        }

        ?>

        <p>
            ¿No tenés una cuenta?

            <a href="registro.php">
                Crear cuenta
            </a>
        </p>

    </section>

</main>

</body>

</html>