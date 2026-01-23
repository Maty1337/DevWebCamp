import Swal from "sweetalert2";

(function(){
    let eventos = [];
    const resumen = document.querySelector('#registro-resumen');

    if(resumen){

    const eventosBoton = document.querySelectorAll('.evento__agregar');
    eventosBoton.forEach(boton => boton.addEventListener('click', seleccionarEvento))

    const formularioRegistro = document.querySelector('#registro');
    formularioRegistro.addEventListener('submit', submitFormulario);

    mostrarEventosSeleccionados();

    function seleccionarEvento(e){

    if(eventos.length < 5){
        const boton = e.currentTarget;
        eventos = [...eventos, {
            id: boton.dataset.id,
            titulo: boton.parentElement.querySelector('.evento__nombre').textContent.trim()
        }];
        boton.disabled = true;
        mostrarEventosSeleccionados();   
    } else {
        Swal.fire({
            title: 'Error',
            text: 'Solo puedes seleccionar 5 eventos',
            icon: 'error',
            confirmButtonText: 'Ok'
            });     
        }
    }

    function mostrarEventosSeleccionados(){
        limpiarEventosSeleccionados();

        if(eventos.length > 0){
            eventos.forEach( evento => {
                const eventoDOM = document.createElement('div');
                eventoDOM.classList.add('registro__evento');

                const titulo = document.createElement('H3');
                titulo.classList.add('registro__nombre');
                titulo.textContent = evento.titulo;

                const botonEliminar = document.createElement('button');
                botonEliminar.classList.add('registro__eliminar');
                botonEliminar.innerHTML = `<i class="fa-solid fa-trash"></i>`;
                botonEliminar.onclick = function(){
                    eliminarEvento(evento.id);
                }

                eventoDOM.appendChild(titulo);
                eventoDOM.appendChild(botonEliminar);
                resumen.appendChild(eventoDOM);
            })
        }else {
            const noRegistros = document.createElement('p');
            noRegistros.textContent = 'No hay eventos seleccionados';
            noRegistros.classList.add('registro__no-eventos');
            resumen.appendChild(noRegistros);
        }
    }

    function eliminarEvento(id){
        eventos = eventos.filter( evento => evento.id !== id);
        const botonAgregar = document.querySelector(`[data-id="${id}"]`);
        botonAgregar.disabled = false;
        mostrarEventosSeleccionados();
    }

    function limpiarEventosSeleccionados(){
        while(resumen.firstChild){
            resumen.removeChild(resumen.firstChild);
        }
    }

    async function submitFormulario(e){
        e.preventDefault();
        const regaloId = document.querySelector('#regalo').value;

        const eventosId = eventos.map( evento => evento.id );

        if(eventosId.length === 0 || regaloId === ''){
            Swal.fire({
                title: 'Error',
                text: 'Debes seleccionar al menos un evento y un regalo',
                icon: 'error',
                confirmButtonText: 'Ok'
                }); 
                return;
        }

        //Objeto de FormData
        const datos = new FormData();
        datos.append('eventos', eventosId);
        datos.append('regalo_id', regaloId);

        const url = '/finalizar-registro/conferencias'
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        })

        const resultado = await respuesta.json();

        if(resultado.resultado){
            Swal.fire({
                title: 'Registro exitoso',
                text: 'Te has registrado correctamente en los eventos',
                icon: 'success',
            }).then(() => location.href = `/boleto?id=${resultado.token}`);
        }else {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al procesar tu registro o tu pase no incluye conferencias',
                icon: 'error',
                confirmButtonText: 'Ok'
                }).then(() => location.reload());
        }

    }
}
})();