
const urlBase = "index.php"

$(function () {
    fetch(urlBase + "?option=talleres_json")
        .then(response => response.json())
        .then(data => {
            agregarTalleres(Array.isArray(data) ? data : [data]);
        })
})

const agregarTalleres = (data) => {
    let bodyTabla = $('#tablaTalleres');

    data.forEach(taller => {
        const filaNueva = `
        <tr>
            <td>${taller.id}</td>
            <td>${taller.nombre}</td>
            <td>${taller.descripcion}</td>
            <td>${taller.cupo_maximo}</td>
            <td>${taller.cupo_disponible}</td>
            <td><button id="btn-agregar" class="btn-solicitar" data-id="${taller.id}">Solicitar taller</button></td>
        </tr>
        `;
        bodyTabla.append(filaNueva);
    })
}



