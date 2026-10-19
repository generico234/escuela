Parece que hay algunos problemas en tu proyecto. Vamos a corregirlos uno por uno:

### Problema 1: El archivo `index.html` no funciona correctamente y solo muestra código

El problema es que estás tratando de acceder a archivos PHP desde un archivo HTML, pero no has configurado correctamente tu servidor o el servidor web no está interpretando PHP.

**Solución:**

1. Asegúrate de estar ejecutando tu proyecto en un servidor local como **XAMPP**, **WAMP**, **MAMP** o un servidor Apache configurado con PHP.
2. Los archivos con extensión `.php` solo se ejecutarán correctamente en un servidor con PHP instalado. Si abres los archivos directamente desde el navegador (por ejemplo, `file:///`), el navegador no podrá interpretar PHP, solo mostrará el código fuente.

**Pasos:**

1. Coloca todos los archivos en la carpeta `htdocs` (en XAMPP) o en la carpeta raíz del servidor que estés usando.
2. Accede a la URL correcta. Por ejemplo, si estás usando XAMPP:

   * Inicia el servidor Apache desde el panel de control de XAMPP.
   * Ve a tu navegador y accede a `http://localhost/nombre_de_tu_proyecto/index.html`.

### Problema 2: El archivo `borrar.php` está vacío

Te proporcionaré el código para el archivo `borrar.php`, de modo que puedas eliminar registros correctamente de la base de datos.

### 3. `borrar.php`

Este archivo permitirá eliminar un registro específico de la base de datos. Este archivo recibirá el `id` del registro y lo borrará.

```php
<?php
$servername = "localhost";
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "personas";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id_per = $_GET['id'];

    // Preparar la consulta para borrar
    $sql = "DELETE FROM registro WHERE id_per = '$id_per'";

    if ($conn->query($sql) === TRUE) {
        echo "Registro eliminado correctamente";
    } else {
        echo "Error al eliminar el registro: " . $conn->error;
    }
}

$conn->close();

// Redirigir al usuario a la página de consulta después de eliminar
header("Location: consulta.php");
exit;
?>
```

### Explicación del código `borrar.php`:

1. **Conexión a la base de datos**: Primero, nos conectamos a la base de datos `personas`.
2. **Recibimos el `id` a través de `GET`**: El `id` del registro que quieres borrar se pasa como parámetro en la URL (por ejemplo, `borrar.php?id=5`).
3. **Ejecutamos la consulta DELETE**: Con la consulta SQL `DELETE`, eliminamos el registro que tiene el `id_per` correspondiente.
4. **Redirigimos a la página de consulta**: Una vez que se borra el registro, redirigimos al usuario de nuevo a la página `consulta.php`.

---

### Asegurémonos de que todo está funcionando bien

1. **Archivos PHP**: Todos los archivos PHP necesitan estar en un servidor web local. Si no tienes configurado un servidor como XAMPP o MAMP, te recomiendo hacerlo.

2. **Configura la base de datos**: Asegúrate de que la base de datos `personas` y la tabla `registro` estén correctamente creadas en MySQL, tal como aparece en el código inicial que proporcionaste. Si no tienes la base de datos, usa `phpMyAdmin` para crearla, o puedes ejecutar la sentencia SQL desde una consola MySQL.

---

### ¿Cómo probarlo todo?

1. **Verifica que tu servidor está corriendo** (por ejemplo, en XAMPP el servidor Apache debe estar activo).
2. **Accede al archivo `index.html`** desde el navegador con `http://localhost/nombre_de_tu_proyecto/index.html` si lo tienes en la carpeta `htdocs`.
3. **Accede a los demás archivos PHP** como `alta.php`, `consulta.php`, `modificar.php` y `borrar.php` directamente desde tu navegador.

---

Si sigues teniendo problemas o si necesitas más detalles sobre cómo configurar tu entorno de desarrollo, no dudes en preguntar. ¡Estoy aquí para ayudarte!
