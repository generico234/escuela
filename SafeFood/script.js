const botonIntolerancias = document.getElementById(
    "botonIntolerancias"
);

const menuIntolerancias = document.getElementById(
    "menuIntolerancias"
);

const buscador = document.getElementById("buscador");

const checkboxes = document.querySelectorAll(
    '#menuIntolerancias input[type="checkbox"]'
);

checkboxes.forEach(function (checkbox) {

    checkbox.addEventListener("change", filtrarLugares);

});

buscador.addEventListener(
    "input",
    filtrarLugares
);


function filtrarLugares() {

    // Intolerancias seleccionadas por el usuario
    const seleccionadas = Array.from(checkboxes)
        .filter(function (checkbox) {
            return checkbox.checked;
        })
        .map(function (checkbox) {
            return checkbox.value
                .trim()
                .toLowerCase();
        });


    // =========================
    // BUSCADOR
    // =========================

    const textoBusqueda = buscador.value
        .trim()
        .toLowerCase();


    console.log("Intolerancias seleccionadas:", seleccionadas);
    console.log("Texto buscado:", textoBusqueda);


    const lugaresFiltrados = lugares.filter(function (lugar) {

        // =========================
        // BUSCAR POR NOMBRE
        // =========================

        const coincideNombre =
            lugar.nombre
                .toLowerCase()
                .includes(textoBusqueda);


        // Intolerancias para las que el lugar es apto
        const intoleranciasLugar = Array.isArray(lugar.intolerancias)
            ? lugar.intolerancias.map(function (intolerancia) {
                return String(intolerancia)
                    .trim()
                    .toLowerCase();
            })
            : [];


        // Si no seleccionamos nada,
        // mostramos todos los lugares.
        if (seleccionadas.length === 0) {

            return coincideNombre;

        }


        // TODAS las intolerancias seleccionadas
        // deben estar entre las aptas del restaurante.
        const cumpleIntolerancias =
            seleccionadas.every(function (intolerancia) {

                return intoleranciasLugar.includes(
                    intolerancia
                );

            });


        // Debe coincidir el nombre
        // Y cumplir las intolerancias
        return (
            coincideNombre &&
            cumpleIntolerancias
        );

    });


    console.log(
        "Lugares filtrados:",
        lugaresFiltrados
    );


    mostrarLugares(lugaresFiltrados);
}

// Abrir y cerrar el menú
botonIntolerancias.addEventListener("click", function () {

    menuIntolerancias.classList.toggle("mostrar");

});


// Cerrar el menú si hacemos clic fuera
document.addEventListener("click", function (evento) {

    if (
        !botonIntolerancias.contains(evento.target) &&
        !menuIntolerancias.contains(evento.target)
    ) {

        menuIntolerancias.classList.remove("mostrar");

    }

});

// =========================
// MAPA
// =========================

const mapa = L.map("mapa").setView(
    [-38.2711, -57.8356],
    14
);

// Mapa de OpenStreetMap
L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors"
    }
).addTo(mapa);




/* =========================
   PANEL LATERAL Y MARCADORES
========================= */

const panelLugar = document.getElementById("panelLugar");
const contenidoPanelLugar = document.getElementById("contenidoPanelLugar");
const cerrarPanelLugar = document.getElementById("cerrarPanelLugar");

let marcadores = [];

function cerrarPanel() {
    panelLugar.classList.remove("abierto");
}

cerrarPanelLugar.addEventListener("click", cerrarPanel);


/* =========================
   FUNCIONES AUXILIARES
========================= */

// Evita que los textos ingresados se interpreten como HTML.
function escaparHTML(valor) {
    const caracteres = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
    };

    return String(valor ?? "").replace(
        /[&<>"']/g,
        function (caracter) {
            return caracteres[caracter];
        }
    );
}

// Acepta únicamente enlaces HTTP o HTTPS.
function obtenerUrlHttp(valor) {
    try {
        const url = new URL(String(valor ?? "").trim());

        if (
            url.protocol === "https:" ||
            url.protocol === "http:"
        ) {
            return url.href;
        }
    } catch (error) {
        return "";
    }

    return "";
}

// Acepta un usuario de Instagram o un enlace de Instagram.
function obtenerUrlInstagram(valor) {
    let texto = String(valor ?? "").trim();

    if (!texto) {
        return "";
    }

    if (/^https?:\/\//i.test(texto)) {
        const urlSegura = obtenerUrlHttp(texto);

        if (!urlSegura) {
            return "";
        }

        try {
            const url = new URL(urlSegura);

            return [
                "instagram.com",
                "www.instagram.com"
            ].includes(url.hostname.toLowerCase())
                ? urlSegura
                : "";
        } catch (error) {
            return "";
        }
    }

    texto = texto
        .replace(/^@/, "")
        .replace(/^(www\.)?instagram\.com\//i, "")
        .replace(/\/.*$/, "");

    if (!/^[a-zA-Z0-9._]+$/.test(texto)) {
        return "";
    }

    return `https://www.instagram.com/${encodeURIComponent(texto)}/`;
}



function mostrarLugares(lugaresMostrar) {

    // Cerrar el panel al actualizar los marcadores.
    cerrarPanel();

    // Borrar marcadores anteriores.
    marcadores.forEach(function (marcador) {
        mapa.removeLayer(marcador);
    });

    marcadores = [];

    // Crear los nuevos marcadores.
    lugaresMostrar.forEach(function (lugar) {

        const marcador = L.marker([
            lugar.lat,
            lugar.lng
        ]).addTo(mapa);

        marcador.on("click", function () {

            // =========================
            // PREPARAR LOS DATOS
            // =========================

            const nombre = escaparHTML(
                lugar.nombre || "Local sin nombre"
            );

            const horario = escaparHTML(
                lugar.horario || ""
            );

            const descripcion = escaparHTML(
                lugar.descripcion || ""
            );

            const direccion = escaparHTML(
                lugar.direccion || ""
            );

            const telefono = String(
                lugar.telefono || ""
            ).trim();

            const telefonoLink = telefono.replace(
                /[^\d+]/g,
                ""
            );

            const whatsapp = String(
                lugar.whatsapp || ""
            ).replace(/\D/g, "");

            const instagramUrl = obtenerUrlInstagram(
                lugar.instagram
            );

            const tipo = escaparHTML(
                lugar.tipo_establecimiento || ""
            );

            const precios = escaparHTML(
                lugar.rango_precios || ""
            );

            const accesibilidad = escaparHTML(
                lugar.accesibilidad || ""
            );

            const menuUrl = obtenerUrlHttp(
                lugar.menu_url
            );

            const metodosPago = String(
                lugar.metodos_pago || ""
            )
                .split(",")
                .map(function (metodo) {
                    return metodo.trim();
                })
                .filter(Boolean);

            const intolerancias = Array.isArray(
                lugar.intolerancias
            )
                ? lugar.intolerancias
                : [];

            const intoleranciasHTML =
                intolerancias.length > 0
                    ? intolerancias.map(function (intolerancia) {
                        return `
                            <span class="etiqueta">
                                ✓ ${escaparHTML(intolerancia)}
                            </span>
                        `;
                    }).join("")
                    : "<span>Sin información</span>";

            const pagosHTML =
                metodosPago.length > 0
                    ? metodosPago.map(function (metodo) {
                        return `
                            <span class="etiqueta">
                                ${escaparHTML(metodo)}
                            </span>
                        `;
                    }).join("")
                    : "<span>Sin información</span>";

            // =========================
            // CONSTRUIR EL PANEL
            // =========================

            contenidoPanelLugar.innerHTML = `

                <div class="popup-lugar">

                    <h3>${nombre}</h3>

                    ${
                        tipo
                            ? `<p><strong>Tipo de local:</strong> ${tipo}</p>`
                            : ""
                    }

                    ${
                        horario
                            ? `<p><strong>Horario:</strong> ${horario}</p>`
                            : ""
                    }

                    ${
                        direccion
                            ? `<p><strong>Dirección:</strong> ${direccion}</p>`
                            : ""
                    }

                    ${
                        descripcion
                            ? `
                                <div class="descripcion-lugar">
                                    <h4>Sobre el local</h4>
                                    <p>${descripcion}</p>
                                </div>
                            `
                            : ""
                    }

                    ${
                        precios
                            ? `
                                <hr>
                                <p><strong>Rango de precios:</strong> ${precios}</p>
                            `
                            : ""
                    }

                    <hr>

                    <h4>Apto para</h4>

                    <div class="opciones">
                        ${intoleranciasHTML}
                    </div>

                    <hr>

                    <h4>Servicios</h4>

                    <div class="opciones">

                        ${
                            Number(lugar.delivery) === 1
                                ? `<span class="etiqueta">Delivery disponible</span>`
                                : ""
                        }

                        ${
                            Number(lugar.take_away) === 1
                                ? `<span class="etiqueta">Retiro en el local</span>`
                                : ""
                        }

                        ${
                            Number(lugar.delivery) !== 1 &&
                            Number(lugar.take_away) !== 1
                                ? "<span>Sin información sobre servicios de entrega</span>"
                                : ""
                        }

                    </div>

                    ${
                        metodosPago.length > 0
                            ? `
                                <hr>
                                <h4>Métodos de pago</h4>
                                <div class="opciones">
                                    ${pagosHTML}
                                </div>
                            `
                            : ""
                    }

                    ${
                        accesibilidad
                            ? `
                                <hr>
                                <h4>Accesibilidad</h4>
                                <p>${accesibilidad}</p>
                            `
                            : ""
                    }

                    ${
                        telefono || whatsapp || instagramUrl
                            ? `
                                <hr>
                                <h4>Contacto</h4>

                                <div class="contactos-lugar">

                                    ${
                                        telefonoLink
                                            ? `
                                                <p>
                                                    <strong>Teléfono:</strong>
                                                    <a href="tel:${escaparHTML(telefonoLink)}">
                                                        ${escaparHTML(telefono)}
                                                    </a>
                                                </p>
                                            `
                                            : ""
                                    }

                                    ${
                                        whatsapp
                                            ? `
                                                <p>
                                                    <a
                                                        href="https://wa.me/${whatsapp}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    >
                                                        Contactar por WhatsApp
                                                    </a>
                                                </p>
                                            `
                                            : ""
                                    }

                                    ${
                                        instagramUrl
                                            ? `
                                                <p>
                                                    <a
                                                        href="${escaparHTML(instagramUrl)}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    >
                                                        Ver Instagram
                                                    </a>
                                                </p>
                                            `
                                            : ""
                                    }

                                </div>
                            `
                            : ""
                    }

                    ${
                        menuUrl
                            ? `
                                <hr>
                                <p>
                                    <a
                                        href="${escaparHTML(menuUrl)}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Ver menú del local
                                    </a>
                                </p>
                            `
                            : ""
                    }

                    <hr>

                    <div class="seccion-comentarios">

                        <h4>Comentarios</h4>

                        <div
                            id="comentarios-${Number(lugar.id)}"
                            class="lista-comentarios"
                        >
                            Cargando comentarios...
                        </div>

                        <div
                            id="formulario-comentario-${Number(lugar.id)}"
                            class="formulario-comentario"
                        ></div>

                    </div>

                </div>
            `;

            // Abrir el panel lateral.
            panelLugar.classList.add("abierto");

            // Cargar los comentarios de este local.
            cargarComentarios(lugar.id);

        });

        marcadores.push(marcador);

    });

}



// =========================
// COMENTARIOS
// =========================

async function cargarComentarios(idLugar) {

    const contenedor =
        document.getElementById(
            `comentarios-${idLugar}`
        );

    const formulario =
        document.getElementById(
            `formulario-comentario-${idLugar}`
        );


    if (!contenedor || !formulario) {
        return;
    }


    try {

        // =========================
        // OBTENER COMENTARIOS
        // =========================

        const respuesta =
            await fetch(
                `obtener-comentarios.php?id_lugar=${idLugar}`,
                {
                    cache: "no-store"
                }
            );


        const datos =
            await respuesta.json();


        if (!datos.exito) {

            contenedor.innerHTML =
                "<p>No se pudieron cargar los comentarios.</p>";

            return;
        }

// =========================
// MOSTRAR COMENTARIOS
// =========================

if (datos.comentarios.length === 0) {

    contenedor.innerHTML = `
        <div class="sin-comentarios">

            <div class="icono-sin-comentarios">
                💬
            </div>

            <strong>
                Todavía no hay comentarios
            </strong>

            <p>
                Sé el primero en compartir tu experiencia.
            </p>

        </div>
    `;

} else {

    contenedor.innerHTML =
        datos.comentarios
            .map(function (comentario) {

                return `
                    <div class="comentario">

                        <div class="cabecera-comentario">

                            <div class="usuario-comentario">

                                <div class="avatar-comentario">
                                    ${comentario.usuario.charAt(0).toUpperCase()}
                                </div>

                                <div>

                                    <strong>
                                        ${comentario.usuario}
                                    </strong>

                                    <small>
                                        ${comentario.creado_en}
                                    </small>

                                </div>

                            </div>

                            ${
                                comentario.puede_eliminar
                                    ? `
                                        <button
                                            type="button"
                                            class="boton-eliminar-comentario"
                                            onclick="eliminarComentario(${comentario.id}, ${idLugar})"
                                            title="Eliminar comentario"
                                        >
                                            🗑️
                                        </button>
                                    `
                                    : ""
                            }

                        </div>

                        <p class="texto-comentario">
                            ${comentario.comentario}
                        </p>

                    </div>
                `;

            })
            .join("");
}


// =========================
// COMPROBAR SESIÓN
// =========================

const respuestaSesion =
    await fetch(
        "estado-sesion.php",
        {
            cache: "no-store"
        }
    );

const sesion =
    await respuestaSesion.json();


// =========================
// MOSTRAR FORMULARIO
// SOLO USUARIO NORMAL
// =========================

if (
    sesion.logueado === true &&
    sesion.rol === "usuario"
) {

    formulario.innerHTML = `

        <div class="titulo-formulario-comentario">
            Dejá tu opinión
        </div>

        <textarea
            id="texto-comentario-${idLugar}"
            placeholder="Contanos qué te pareció este lugar..."
            maxlength="500"
        ></textarea>

        <div class="contador-comentario">
            Máximo 500 caracteres
        </div>

        <button
            type="button"
            class="boton-publicar-comentario"
            onclick="publicarComentario(${idLugar})"
        >
            Publicar comentario
        </button>

    `;

} else {

    formulario.innerHTML = `
        <p class="mensaje-comentario">
            Iniciá sesión con una cuenta de usuario
            para publicar un comentario.
        </p>
    `;

}

    } catch (error) {

        console.error(
            "Error cargando comentarios:",
            error
        );

        contenedor.innerHTML =
            "<p>Error al cargar los comentarios.</p>";
    }
}

async function publicarComentario(idLugar) {

    const textarea =
        document.getElementById(
            `texto-comentario-${idLugar}`
        );

    if (!textarea) {
        return;
    }

    const comentario =
        textarea.value.trim();

    if (comentario === "") {

        alert(
            "Escribí un comentario antes de publicarlo."
        );

        return;
    }

    try {

        const respuesta =
            await fetch(
                "guardar-comentario.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({
                        id_lugar: idLugar,
                        comentario: comentario
                    })
                }
            );

        const datos =
            await respuesta.json();

        if (!datos.exito) {

            alert(
                datos.mensaje
            );

            return;
        }

        textarea.value = "";

        await cargarComentarios(
            idLugar
        );

    } catch (error) {

        console.error(
            "Error publicando comentario:",
            error
        );

        alert(
            "No se pudo publicar el comentario."
        );
    }
}

window.publicarComentario = publicarComentario;

// =========================
// ELIMINAR COMENTARIO
// =========================

async function eliminarComentario(
    idComentario,
    idLugar
) {

    const confirmar =
        confirm(
            "¿Querés eliminar este comentario?"
        );

    if (!confirmar) {
        return;
    }


    try {

        const respuesta =
            await fetch(
                "eliminar-comentario.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({
                        id_comentario:
                            idComentario
                    })
                }
            );


        const textoRespuesta =
            await respuesta.text();


        console.log(
            "Respuesta de eliminar-comentario.php:",
            textoRespuesta
        );


        let datos;


        try {

            datos =
                JSON.parse(
                    textoRespuesta
                );

        } catch (error) {

            console.error(
                "La respuesta de eliminar-comentario.php no es JSON:",
                textoRespuesta
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


        // Volver a cargar los comentarios
        await cargarComentarios(
            idLugar
        );


    } catch (error) {

        console.error(
            "Error eliminando comentario:",
            error
        );

        alert(
            "No se pudo eliminar el comentario."
        );

    }

}


// Permitir usar la función desde el onclick
window.eliminarComentario =
    eliminarComentario;

// =========================
// OBTENER LUGARES DE MYSQL
// =========================

let lugares = [];

async function cargarLugares() {

    try {

        const respuesta = await fetch("obtener-lugares.php");

        const datos = await respuesta.json();

        if (!datos.exito) {

            console.error(
                "Error al obtener lugares:",
                datos.mensaje
            );

            return;
        }

        lugares = datos.lugares;

        console.log(
            "Lugares cargados desde MySQL:",
            lugares
        );

        mostrarLugares(lugares);

    } catch (error) {

        console.error(
            "Error cargando lugares desde MySQL:",
            error
        );

    }
}

cargarLugares();

// =========================
// PANEL DE LOGIN
// =========================

const botonAdmin =
    document.getElementById("botonAdmin");

const panelLogin =
    document.getElementById("panelLogin");


if (botonAdmin && panelLogin) {

    botonAdmin.addEventListener(
        "click",
        function () {

            panelLogin.classList.toggle(
                "mostrar"
            );

        }
    );

}

// =========================
// SESIÓN DEL USUARIO
// =========================

async function comprobarSesion() {

    try {

        const respuesta = await fetch(
            "estado-sesion.php",
            {
                cache: "no-store"
            }
        );

        const datos = await respuesta.json();

        console.log(
            "Datos de sesión:",
            datos
        );

        const estadoSesion =
            document.getElementById("estadoSesion");

        if (!estadoSesion) {

            console.error(
                "No existe #estadoSesion en index.html"
            );

            return;
        }


        // =========================
        // USUARIO LOGUEADO
        // =========================

        if (datos.logueado === true) {

            let botonPanel = "";

                if (datos.rol === "admin") {

                    botonPanel = `
                        <a
                            href="admin/panel.php"
                            class="boton-sesion boton-panel"
                        >
                            Panel admin
                        </a>
                    `;

                } else if (datos.rol === "empresa") {

                    botonPanel = `
                        <a
                            href="empresa/panel.php"
                            class="boton-sesion boton-panel"
                        >
                            Panel empresa
                        </a>
                    `;

                }


            // =========================
            // MOSTRAR SESIÓN
            // =========================

            estadoSesion.innerHTML = `

                <span class="usuario-sesion">
                    Usuario: ${datos.usuario}
                </span>

                ${botonPanel}

                <a
                    href="cerrar-sesion.php"
                    class="boton-sesion boton-cerrar"
                >
                    Cerrar sesión
                </a>

            `;

        }


        // =========================
        // USUARIO NO LOGUEADO
        // =========================

        else {

            estadoSesion.innerHTML = `

                <a
                    href="login.php"
                    class="boton-sesion boton-login"
                >
                    Iniciar sesión
                </a>

                <a
                    href="registro.php"
                    class="boton-sesion boton-registro"
                >
                    Registrarse
                </a>

            `;
        }

    }

    catch (error) {

        console.error(
            "Error comprobando sesión:",
            error
        );

    }

}

comprobarSesion();