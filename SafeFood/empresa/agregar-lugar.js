const mapa = L.map("mapaSeleccion").setView(
    [-38.2711, -57.8356],
    14
);

L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors"
    }
).addTo(mapa);


let marcador = null;

let latitudSeleccionada = null;
let longitudSeleccionada = null;


mapa.on("click", function (evento) {

    latitudSeleccionada =
        evento.latlng.lat;

    longitudSeleccionada =
        evento.latlng.lng;


    if (marcador) {

        marcador.setLatLng(
            evento.latlng
        );

    } else {

        marcador =
            L.marker(
                evento.latlng
            ).addTo(mapa);

    }

});


document
    .getElementById("guardarLugar")
    .addEventListener(
        "click",
        async function () {

            const nombre =
                document
                    .getElementById("nombre")
                    .value
                    .trim();


            const horario =
                document
                    .getElementById("horario")
                    .value
                    .trim();


            const descripcion =
                document
                    .getElementById("descripcion")
                    .value
                    .trim();


            const direccion =
                document.getElementById("direccion").value.trim();

            const telefono =
                document.getElementById("telefono").value.trim();

            const whatsapp =
                document.getElementById("whatsapp").value.trim();

            const instagram =
                document.getElementById("instagram").value.trim();

            const tipo_establecimiento =
                document.getElementById("tipo_establecimiento").value;

            const rango_precios =
                document.getElementById("rango_precios").value;

            const metodos_pago = Array.from(
                document.querySelectorAll(
                    'input[name="metodos_pago"]:checked'
                )
            ).map(input => input.value);

            const delivery =
                document.getElementById("delivery").checked;

            const take_away =
                document.getElementById("take_away").checked;

            const accesibilidad =
                document.getElementById("accesibilidad").value.trim();

            const menu_url =
                document.getElementById("menu_url").value.trim();

            const intolerancias =
                Array.from(
                    document.querySelectorAll(
                        '.opciones input[type="checkbox"]:checked'
                    )
                ).map(
                    function (checkbox) {
                        return parseInt(
                            checkbox.value
                        );
                    }
                );


            if (nombre === "") {

                alert(
                    "Ingresá el nombre del local."
                );

                return;
            }


            if (horario === "") {

                alert(
                    "Ingresá el horario del local."
                );

                return;
            }


            if (
                latitudSeleccionada === null ||
                longitudSeleccionada === null
            ) {

                alert(
                    "Seleccioná la ubicación del local en el mapa."
                );

                return;
            }


            try {

                const respuesta =
                    await fetch(
                        "../guardar-lugar-empresa.php",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },


                            body: JSON.stringify({
                                nombre,
                                horario,
                                descripcion,
                                direccion,
                                telefono,
                                whatsapp,
                                instagram,
                                tipo_establecimiento,
                                rango_precios,
                                metodos_pago,
                                delivery,
                                take_away,
                                accesibilidad,
                                menu_url,
                                latitud: latitudSeleccionada,
                                longitud: longitudSeleccionada,
                                intolerancias
                            })
                        }
                    );


                const texto =
                    await respuesta.text();


                console.log(
                    "Respuesta:",
                    texto
                );


                let datos;

                try {

                    datos =
                        JSON.parse(
                            texto
                        );

                } catch (error) {

                    console.error(
                        "Respuesta no válida:",
                        texto
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


                alert(
                    "Local registrado correctamente."
                );


                window.location.href =
                    "panel.php";


            } catch (error) {

                console.error(
                    "Error:",
                    error
                );

                alert(
                    "No se pudo registrar el local."
                );

            }

        }
    );