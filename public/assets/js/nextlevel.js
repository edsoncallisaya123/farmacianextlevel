/* Utilidades del sistema Farmacia Next Level */
document.addEventListener('DOMContentLoaded', function () {
    // Menú lateral en móviles
    var btn = document.getElementById('btnMenu');
    var side = document.getElementById('sidebar');
    if (btn && side) {
        btn.addEventListener('click', function () { side.classList.toggle('abierto'); });
    }

    // Tablas con búsqueda y paginación
    document.querySelectorAll('table[data-tabla]').forEach(iniciarTabla);

    // Exportar tablas a CSV
    document.querySelectorAll('[data-exportar]').forEach(function (b) {
        b.addEventListener('click', function (e) {
            e.preventDefault();
            exportarCsv(document.querySelector(b.getAttribute('data-exportar')), b.getAttribute('data-nombre') || 'datos');
        });
    });

    // Confirmación al enviar formularios de eliminación
    document.querySelectorAll('form[data-confirmar]').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (!confirm(f.getAttribute('data-confirmar'))) { e.preventDefault(); }
        });
    });
});

function iniciarTabla(tabla) {
    var cuerpo = tabla.tBodies[0];
    if (!cuerpo) { return; }
    var filas = Array.prototype.slice.call(cuerpo.rows).filter(function (r) { return !r.classList.contains('fila-vacia'); });
    if (filas.length === 0) { return; }

    var tam = parseInt(tabla.getAttribute('data-tam') || '5', 10);
    var pagina = 1;
    var texto = '';
    var envoltura = tabla.closest('.table-responsive') || tabla;

    var barra = document.createElement('div');
    barra.className = 'd-flex justify-content-between align-items-end flex-wrap gap-2 mb-2 no-imprimir';
    barra.innerHTML =
        '<div><label class="form-label mb-0">Mostrar</label><br>' +
        '<select class="form-select form-select-sm" style="width:80px">' +
        '<option>5</option><option>10</option><option>25</option><option>50</option></select>' +
        '<div class="small text-muted mt-1">registros por página</div></div>' +
        '<div><label class="form-label mb-0"><i class="bi bi-search"></i> Buscar:</label>' +
        '<input type="search" class="form-control form-control-sm" style="min-width:200px"></div>';
    envoltura.parentNode.insertBefore(barra, envoltura);

    var pie = document.createElement('div');
    pie.className = 'd-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 no-imprimir';
    envoltura.parentNode.insertBefore(pie, envoltura.nextSibling);

    var selTam = barra.querySelector('select');
    selTam.value = String(tam);
    if (selTam.value !== String(tam)) { selTam.value = '5'; tam = 5; }
    var buscador = barra.querySelector('input');

    function pintar() {
        var visibles = filas.filter(function (f) { return f.textContent.toLowerCase().indexOf(texto) !== -1; });
        var paginas = Math.max(1, Math.ceil(visibles.length / tam));
        if (pagina > paginas) { pagina = paginas; }
        filas.forEach(function (f) { f.style.display = 'none'; });
        visibles.slice((pagina - 1) * tam, pagina * tam).forEach(function (f) { f.style.display = ''; });

        pie.innerHTML = '';
        var info = document.createElement('div');
        info.className = 'small text-muted';
        info.textContent = visibles.length === 0 ? 'Sin resultados' : 'Mostrando página ' + pagina + ' de ' + paginas;
        pie.appendChild(info);

        var nav = document.createElement('div');
        nav.className = 'btn-group btn-group-sm';
        function boton(etq, destino, activo, deshabilitado) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'btn ' + (activo ? 'btn-nl' : 'btn-outline-secondary');
            b.textContent = etq;
            b.disabled = !!deshabilitado;
            b.addEventListener('click', function () { pagina = destino; pintar(); });
            nav.appendChild(b);
        }
        boton('‹', pagina - 1, false, pagina <= 1);
        for (var i = 1; i <= paginas && i <= 7; i++) { boton(String(i), i, i === pagina, false); }
        boton('›', pagina + 1, false, pagina >= paginas);
        pie.appendChild(nav);
    }

    selTam.addEventListener('change', function () { tam = parseInt(selTam.value, 10); pagina = 1; pintar(); });
    buscador.addEventListener('input', function () { texto = buscador.value.toLowerCase(); pagina = 1; pintar(); });
    pintar();
}

function exportarCsv(tabla, nombre) {
    if (!tabla) { return; }
    var lineas = [];
    var cabeceras = Array.prototype.slice.call(tabla.tHead.rows[0].cells);
    var excluir = [];
    cabeceras.forEach(function (c, i) { if (c.classList.contains('no-exportar')) { excluir.push(i); } });
    function fila(celdas) {
        return celdas.filter(function (c, i) { return excluir.indexOf(i) === -1; })
            .map(function (c) { return '"' + c.textContent.replace(/\s+/g, ' ').trim().replace(/"/g, '""') + '"'; })
            .join(';');
    }
    lineas.push(fila(cabeceras));
    Array.prototype.slice.call(tabla.tBodies[0].rows).forEach(function (r) {
        if (r.classList.contains('fila-vacia')) { return; }
        lineas.push(fila(Array.prototype.slice.call(r.cells)));
    });
    var blob = new Blob(['\ufeff' + lineas.join('\n')], { type: 'text/csv;charset=utf-8;' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = nombre + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
