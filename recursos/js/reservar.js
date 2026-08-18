/*
   Filtra las actividades del formulario de reserva segun el hotel elegido.

   La idea es sencilla: cada hotel pertenece a un destino y cada actividad
   tambien. Si el cliente escoge un hotel en La Fortuna, no tiene sentido
   ofrecerle actividades de Puerto Viejo.

   Para lograrlo, en el HTML se guardo el destino de cada uno con el
   atributo data-destino:

       <option value="1" data-destino="2">   <- hotel del destino 2
       <label data-destino="2">              <- actividad del destino 2

   Cuando el cliente cambia el hotel, se recorren las actividades y se
   esconden las que no sean de ese mismo destino.

   Ojo: esto es solo una ayuda visual. La validacion de verdad se hace
   en el servidor (ClienteController), porque el JavaScript se puede
   desactivar desde el navegador.
*/
(function () {
    var selectHotel = document.getElementById('id_hotel');
    var aviso = document.getElementById('avisoSinActividades');

    // Si la pagina no tiene el formulario de reserva, no se hace nada.
    if (!selectHotel) {
        return;
    }

    // Todas las actividades que se pintaron en la pagina.
    var actividades = document.querySelectorAll('.checkbox-lista label[data-destino]');

    function filtrarActividades() {
        // Se busca la opcion que el cliente tiene seleccionada.
        var opcion = selectHotel.options[selectHotel.selectedIndex];
        var destinoHotel = opcion ? opcion.getAttribute('data-destino') : null;

        var visibles = 0;

        for (var i = 0; i < actividades.length; i++) {
            var fila = actividades[i];
            var casilla = fila.querySelector('input[type="checkbox"]');

            // Si todavia no se escoge hotel, se muestran todas.
            var coincide = (destinoHotel === null) || (fila.getAttribute('data-destino') === destinoHotel);

            fila.hidden = !coincide;

            if (coincide) {
                visibles++;
            } else if (casilla) {
                // Si una actividad queda escondida, se desmarca para que
                // no se envie por error al guardar la reserva.
                casilla.checked = false;
            }
        }

        // El aviso solo sale cuando ya se escogio hotel y no quedo ninguna.
        if (aviso) {
            aviso.hidden = !(destinoHotel !== null && visibles === 0);
        }
    }

    selectHotel.addEventListener('change', filtrarActividades);

    // Se ejecuta una vez al cargar, por si el hotel venia preseleccionado
    // desde la pagina del destino.
    filtrarActividades();
})();
