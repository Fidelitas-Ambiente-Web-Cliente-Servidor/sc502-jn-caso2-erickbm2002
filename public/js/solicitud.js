const urlBase1 = "index.php"

$(function () {
    
    fetch(urlBase1 + '?option=solicitudes_json')
    .then(response => response.json())
        .then(data => {
            const lista = data.data.solicitudes;
            cargarSolicitudes(Array.isArray(lista) ? lista : [lista]);
    })
  $(document).on("click", ".btn-solicitar" ,function () {
        const fila = $(this).closest('tr');
        solicitarCurso(fila);
  })
    
    $(document).on("click", ".btn-aprobar", function () {
        const id = $(this).data('id');
        const fila = $(this).closest('tr');
        procesarSolicitud(id, "aprobar", fila);
    });

    $(document).on("click", ".btn-rechazar", function () {
        const id = $(this).data('id');
        const fila = $(this).closest('tr');
        procesarSolicitud(id, "rechazar", fila);
    });
})


const solicitarCurso = (fila) => {
    const datos = new FormData();
    const tallerId = fila.find('.btn-solicitar').data('id'); 
    
    datos.append("option", "solicitar");
    datos.append("taller_id", tallerId); 

    fetch(urlBase, {
        method: "POST",
        body: datos
    })
    .then(response => response.json())
        .then(data => {
        if (data.response == "00") {
            actualizarCuposDiponibles(fila, data.data["cupo_disponible"]);
        } 
        alert(data.mensaje);
    });
}

const actualizarCuposDiponibles = (fila,datoNuevo) =>  {
    const campoCupoDisponible = fila.find(('td:eq(4)')).text(datoNuevo);
}


const cargarSolicitudes = (data) => { 
    let bodyTabla = $('#solicitudes-body');
    let spanSolicitudesVacias = $('#sin-solicitudes');
    if (!data) return spanSolicitudesVacias.text("Sin solicitudes");
    
    data.forEach(solicitud => {
        const filaNueva = `
<tr>
    <td>${solicitud.id}</td>
    <td>${solicitud.descripcion}</td>
    <td>${solicitud.username}</td>
    <td>${solicitud.fecha_solicitud}</td>
    <td>
        <div>
            <button class="btn-aprobar" data-id="${solicitud.id}">Aprobar</button>
            <button class="btn-rechazar" data-id="${solicitud.id}">Rechazar</button>
        </div>
    </td>
</tr>
`;
        bodyTabla.append(filaNueva);
    })
}


const procesarSolicitud = (id, accion, fila) => {
    const datos = new FormData();
    datos.append("option", accion); 
    datos.append("id_solicitud", id);

    fetch(urlBase1, {
        method: "POST",
        body: datos
    })
    .then(response => response.json())
        .then(data => {
        console.log("vamos bien")
            if (data.success) {
            console.log("vamos bien 2")
            alert(data.mensaje);
            fila.remove(); 
        } else {
            console.log("entre mal ")
            alert("Error: " + data.error);
        }
    });
}
