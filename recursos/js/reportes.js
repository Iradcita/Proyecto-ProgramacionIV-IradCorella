// Activa graficos simples y filtros de tablas para el panel de reportes.
(function () {
    var fuente = document.getElementById('datosReportes');
    var datos = {};

    if (fuente) {
        try {
            datos = JSON.parse(fuente.textContent || '{}');
        } catch (error) {
            datos = {};
        }
    }

    function pintarGrafico(contenedor) {
        var nombre = contenedor.getAttribute('data-chart');
        var filas = datos[nombre] || [];
        var maximo = filas.reduce(function (mayor, fila) {
            return Math.max(mayor, Number(fila.value) || 0);
        }, 0);

        contenedor.innerHTML = '';

        if (!filas.length || maximo === 0) {
            contenedor.innerHTML = '<p class="texto-muted">No hay datos suficientes.</p>';
            return;
        }

        filas.slice(0, 8).forEach(function (fila) {
            var valor = Number(fila.value) || 0;
            var porcentaje = Math.max(4, Math.round((valor / maximo) * 100));
            var item = document.createElement('div');
            item.className = 'grafico-barras__fila';
            item.innerHTML = '<span title="' + escapar(fila.label) + '">' + escapar(fila.label) + '</span>'
                + '<div><i style="width:' + porcentaje + '%"></i></div>'
                + '<strong>' + valor + '</strong>';
            contenedor.appendChild(item);
        });
    }

    function escapar(texto) {
        return String(texto || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.querySelectorAll('[data-chart]').forEach(pintarGrafico);

    /* ----------------------------------------------------------------
       FILTRO DE LAS TABLAS DE REPORTES

       Al escribir en el buscador se recorren las filas de las seis
       tablas y se esconden las que no contienen el texto buscado.
       Si una tabla se queda sin filas visibles, se le muestra un
       mensaje para que no quede vacia y se vea incompleta.
       ---------------------------------------------------------------- */

    var buscador = document.getElementById('buscadorReportes');
    var limpiar = document.getElementById('limpiarReportes');
    var tablas = Array.prototype.slice.call(document.querySelectorAll('.tabla-reporte tbody'));

    // Crea (una sola vez) la fila que dice "no hay coincidencias".
    function obtenerFilaAviso(tbody) {
        var aviso = tbody.querySelector('.sin-resultados');

        if (!aviso) {
            // Se cuentan las columnas del encabezado para que el mensaje
            // ocupe todo el ancho de la tabla.
            var tabla = tbody.closest('table');
            var columnas = tabla.querySelectorAll('thead th').length || 1;

            aviso = document.createElement('tr');
            aviso.className = 'sin-resultados';
            aviso.innerHTML = '<td colspan="' + columnas + '">No hay coincidencias en este reporte.</td>';
            tbody.appendChild(aviso);
        }

        return aviso;
    }

    function filtrarTablas() {
        var texto = (buscador.value || '').toLowerCase();

        tablas.forEach(function (tbody) {
            var visibles = 0;

            Array.prototype.slice.call(tbody.rows).forEach(function (fila) {
                // La fila del aviso no se filtra, se maneja aparte.
                if (fila.classList.contains('sin-resultados')) {
                    return;
                }

                var coincide = texto === '' || fila.textContent.toLowerCase().indexOf(texto) !== -1;
                fila.hidden = !coincide;

                if (coincide) {
                    visibles++;
                }
            });

            // El aviso solo aparece si se busco algo y no quedo ninguna fila.
            obtenerFilaAviso(tbody).hidden = (visibles > 0);
        });
    }

    if (buscador) {
        buscador.addEventListener('input', filtrarTablas);
        filtrarTablas(); // deja todo ordenado al cargar la pagina
    }

    if (limpiar) {
        limpiar.addEventListener('click', function () {
            buscador.value = '';
            filtrarTablas();
            buscador.focus();
        });
    }
})();
