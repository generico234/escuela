// =========================
// MAPA PARA SELECCIONAR UBICACIÓN
// =========================

const mapaSeleccion = L.map(
    "mapaSeleccion"
).setView(
    [-38.2711, -57.8356],
    14
);


L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors"
    }
).addTo(mapaSeleccion);


let marcadorSeleccion = null;

let latitudSeleccionada = null;

let longitudSeleccionada = null;

mapaSeleccion.on(
    "click",
    function (evento) {

        const latitud =
            evento.latlng.lat;

        const longitud =
            evento.latlng.lng;


        latitudSeleccionada =
            latitud;

        longitudSeleccionada =
            longitud;


        // Si ya existe un marcador,
        // lo movemos
        if (marcadorSeleccion) {

            marcadorSeleccion.setLatLng(
                [latitud, longitud]
            );

        }

        // Si no existe, creamos uno
        else {

            marcadorSeleccion =
                L.marker(
                    [latitud, longitud]
                ).addTo(mapaSeleccion);

        }


        document.getElementById(
            "coordenadas"
        ).textContent =
            ` Ubicación seleccionada: ${latitud.toFixed(6)
            }, ${longitud.toFixed(6)
            }`;

    }
);

const formulario = document.getElementById(
    "formularioLugar"
);


const mensaje = document.getElementById(
    "mensaje"
);


formulario.addEventListener(
    "submit",
    async function (evento) {

        evento.preventDefault();


        // =========================
        // OBTENER DATOS
        // =========================

        const nombre =
            document.getElementById("nombre").value.trim();

        const horario =
            document.getElementById("horario").value.trim();

        const descripcion =
            document.getElementById("descripcion").value.trim();

        const intolerancias =
            Array.from(
                document.querySelectorAll(
                    'input[name="intolerancias"]:checked'
                )
            ).map(function (checkbox) {

                return checkbox.value;

            });

        const latitud =
            latitudSeleccionada;

        const longitud =
            longitudSeleccionada;

        if (
            latitudSeleccionada === null ||
            longitudSeleccionada === null
        ) {

            mensaje.textContent =
                " Seleccioná una ubicación en el mapa.";

            return;
        }


        // =========================
        // ENVIAR A PHP
        // =========================

        try {

            const respuesta = await fetch(
                "../guardar-lugar.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({

                        nombre: nombre,

                        horario: horario,

                        descripcion: descripcion,

                        latitud: latitud,

                        longitud: longitud,

                        intolerancias: intolerancias

                    })
                }
            );


            // =========================
            // LEER RESPUESTA DEL SERVIDOR
            // =========================

            const textoRespuesta =
                await respuesta.text();

            console.log(
                "Respuesta de guardar-lugar.php:",
                textoRespuesta
            );


            // Intentar convertir la respuesta a JSON

            let resultado;

            try {

                resultado =
                    JSON.parse(textoRespuesta);

            } catch (error) {

                console.error(
                    "La respuesta de PHP no es JSON válido:",
                    textoRespuesta
                );

                // Como el lugar ya puede haberse guardado,
                // mostramos un mensaje más claro.

                mensaje.textContent =
                    " El lugar fue enviado, pero el servidor devolvió una respuesta inesperada.";

                return;
            }


            // =========================
            // COMPROBAR RESULTADO
            // =========================

            if (resultado.exito) {

                mensaje.textContent =
                    " Lugar guardado correctamente.";

                formulario.reset();

                latitudSeleccionada = null;
                longitudSeleccionada = null;

                document.getElementById(
                    "coordenadas"
                ).textContent =
                    " Todavía no seleccionaste una ubicación.";

            } else {

                mensaje.textContent =
                    " " + resultado.mensaje;

            }


        } catch (error) {

            console.error(
                "Error al guardar el lugar:",
                error
            );

            mensaje.textContent =
                " El lugar se guardó, pero hubo un problema al recibir la respuesta.";

        }

    }
);