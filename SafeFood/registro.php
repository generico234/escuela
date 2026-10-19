<?php
session_start();

if (isset($_SESSION["usuario_id"])) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta - SafeFood</title>

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

        <h1> Crear cuenta</h1>

        <p>
            Creá tu cuenta para poder comentar sobre los lugares.
        </p>


        <form action="registrar-usuario.php" method="POST">


            <label for="usuario">Usuario</label>

            <input
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Ingresá tu usuario"
                required
            >

            <label for="contrasena">Contraseña</label>

            <input
                type="password"
                id="contrasena"
                name="contrasena"
                placeholder="Ingresá tu contraseña"
                required
            >

            <label for="rol">Tipo de cuenta</label>

            <select id="rol" name="rol" required>
                <option value="usuario">Usuario</option>
                <option value="empresa">Empresa</option>
            </select>

            <button type="submit">
                Crear cuenta
            </button>

        </form>

        <p>
            ¿Ya tenés una cuenta?

            <a href="login.php">
                Iniciar sesión
            </a>
        </p>

    </section>

</main>

</body>

</html>