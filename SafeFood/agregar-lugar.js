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


        const latitud =
            parseFloat(
                document.getElementById("latitud").value
            );


        const longitud =
            parseFloat(
                document.getElementById("longitud").value
            );


        const intolerancias =
            Array.from(
                document.querySelectorAll(
                    'input[name="intolerancias"]:checked'
                )
            ).map(function (checkbox) {

                return checkbox.value;

            });


        // =========================
        // ENVIAR A PHP
        // =========================

        try {

            const respuesta = await fetch(
                "guardar-lugar.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    body: JSON.stringify({

                        nombre: nombre,

                        horario: horario,

                        latitud: latitud,

                        longitud: longitud,

                        intolerancias:
                            intolerancias

                    })
                }
            );


            const resultado =
                await respuesta.json();


            // =========================
            // MOSTRAR RESULTADO
            // =========================

            if (resultado.exito) {

                mensaje.textContent =
                    "✅ Lugar guardado correctamente :v";

                formulario.reset();

            } else {

                mensaje.textContent =
                    "❌ " + resultado.mensaje;

            }


        } catch (error) {

            console.error(error);

            mensaje.textContent =
                "❌ Error al conectar con el servidor.";

        }

    }
);