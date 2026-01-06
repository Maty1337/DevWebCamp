(function(){
    const ponentesInput = document.querySelector('#ponentes');

    if(ponentesInput){
        let ponentes = [];
        let ponentesFiltrados = [];

        const listadoPonentes = document.querySelector('#listado-ponentes');
        const ponenteHidden = document.querySelector('[name="ponente_id"]');
        obtenerPonentes();

        ponentesInput.addEventListener('input', buscarPonentes);

        if(ponenteHidden.value){
            (async () => {
                const ponente = await obtenerponente(ponenteHidden.value);
                const {nombre , apellido} = ponente;
                
                //insertar el ponente en el input
                const ponenteDOM = document.createElement('LI');
                ponenteDOM.classList.add('listado-ponentes__ponente', 'listado-ponentes__ponente--seleccionado');
                ponenteDOM.textContent = `${nombre.trim()} ${apellido.trim()}`;

                listadoPonentes.appendChild(ponenteDOM);
            })()    
        }

        async function obtenerPonentes(){
            const url = `/api/ponentes`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            formatearPonentes(resultado);

        }

        async function obtenerponente(id) {
            const url = `/api/ponente?id=${id}`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            return resultado;
        }

        function formatearPonentes(arrayPonentes = []){
            ponentes = arrayPonentes.map( ponente => {
                return{
                    nombre: `${ponente.nombre.trim()} ${ponente.apellido.trim()}`,
                    id: ponente.id
                }
            })

        }

        function buscarPonentes(e){
            const busqueda = e.target.value;
            if(busqueda.length > 3){
                const expresion = new RegExp(busqueda, "i"); //Buscar un patron sin importar mayusculas o minusculas
                ponentesFiltrados = ponentes.filter( ponente => {
                    if(ponente.nombre.toLowerCase().search( expresion ) != -1 ){
                        return ponente;
                    }
                })
                } else {
                    ponentesFiltrados = [];
                }
            mostrarPonentesBuscados();
        }

        function mostrarPonentesBuscados(){

            //limpiar el html previo
            while(listadoPonentes.firstChild){
                listadoPonentes.removeChild(listadoPonentes.firstChild);
            }

            if(ponentesFiltrados.length > 0){
                //mostrar los ponentes
                ponentesFiltrados.forEach( ponente => {
                    const ponenteHTML = document.createElement('LI');
                    ponenteHTML.classList.add('listado-ponentes__ponente');
                    ponenteHTML.textContent = ponente.nombre;
                    ponenteHTML.dataset.ponenteId = ponente.id;
                    ponenteHTML.onclick = seleccionarPonente;

                    //añadir al html
                    listadoPonentes.appendChild(ponenteHTML);
                })
            }else {
                const noResultado = document.createElement('P');
                noResultado.classList.add('listado-ponentes__no-resultado');
                noResultado.textContent = 'No hay resultados';
                listadoPonentes.appendChild(noResultado);
            }
        }

        function seleccionarPonente(e){
            const ponente = e.target;

            //eliminar la seleccion previa
            const ponentePrevio = listadoPonentes.querySelector('.listado-ponentes__ponente--seleccionado');
            if(ponentePrevio){
                ponentePrevio.classList.remove('listado-ponentes__ponente--seleccionado');
            }


            ponente.classList.add('listado-ponentes__ponente--seleccionado');
            ponentesInput.value = ponente.textContent;
            ponenteHidden.value = ponente.dataset.ponenteId;
        }
    }   
})();