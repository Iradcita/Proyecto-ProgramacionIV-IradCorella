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

    var buscador = document.getElementById('buscadorReportes');
    var limpiar = document.getElementById('limpiarReportes');
    var tablas = Array.prototype.slice.call(document.querySelectorAll('.tabla-reporte tbody'));

    function filtrarTablas() {
        var texto = (buscador.value || '').toLowerCase();

        tablas.forEach(function (tbody) {
            Array.prototype.slice.call(tbody.rows).forEach(function (fila) {
                fila.hidden = texto !== '' && fila.textContent.toLowerCase().indexOf(texto) === -1;
            });
        });
    }

    if (buscador) {
        buscador.addEventListener('input', filtrarTablas);
    }

    if (limpiar) {
        limpiar.addEventListener('click', function () {
            buscador.value = '';
            filtrarTablas();
            buscador.focus();
        });
    }
})();
