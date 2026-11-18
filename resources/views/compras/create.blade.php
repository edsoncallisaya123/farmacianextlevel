@extends('layouts.app')
@section('titulo', 'Registrar nueva compra')
@section('migas', 'Compras / Nueva compra')

@section('contenido')
<form method="post" action="{{ route('compras.store') }}" id="formCompra">
    @csrf
    <div class="nl-card">
        <div class="nl-titulo"><h2>Registrar Nueva Compra</h2><a href="{{ route('compras.index') }}" class="btn btn-nl-outline"><i class="bi bi-list"></i> Ver Historial</a></div>
    </div>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="nl-card">
                <div class="nl-sub"><i class="bi bi-boxes"></i> Productos</div>
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-2"><label class="form-label">Cantidad</label><input type="number" id="cant" min="1" value="1" class="form-control"></div>
                    <div class="col-md-4">
                        <label class="form-label">Código de producto</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-upc"></i></span><input id="codigo" class="form-control" placeholder="Ingresar código" autocomplete="off"></div>
                    </div>
                    <div class="col-md-2"><label class="form-label">Costo unit.</label><input type="number" id="costo" min="0" step="0.01" value="0" class="form-control"></div>
                    <div class="col-md-2"><label class="form-label">N.º lote</label><input id="lote" class="form-control" maxlength="50"></div>
                    <div class="col-md-2"><label class="form-label">Vencimiento</label><input type="date" id="venc" class="form-control" min="{{ date('Y-m-d') }}"></div>
                    <div class="col-12 d-flex gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProd"><i class="bi bi-search"></i> Buscar producto</button>
                        <button type="button" class="btn btn-success" id="btnAgregar"><i class="bi bi-plus-lg"></i> Agregar</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table tabla-items table-sm">
                        <thead><tr><th>#</th><th>Código</th><th>Cant.</th><th>Nombre</th><th>Lote</th><th>Vence</th><th class="text-end">Unit.</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                        <tbody id="cuerpo"><tr id="vacio"><td colspan="9" class="text-center text-muted py-3"><i class="bi bi-info-circle"></i> No hay productos agregados</td></tr></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2"><span>Totales: <span id="nItems">0</span></span><span>Total: <span class="total-box">Bs <span id="totalTxt">0.00</span></span></span></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="nl-card">
                <div class="nl-sub"><i class="bi bi-info-circle-fill"></i> Información de Compra</div>
                <div class="mb-3"><label class="form-label">Laboratorio / proveedor *</label>
                    <select name="laboratorio_id" class="form-select" required><option value="">Seleccione un laboratorio</option>
                        @foreach($laboratorios as $l)<option value="{{ $l->id }}" @selected(old('laboratorio_id') == $l->id)>{{ $l->nombre }}</option>@endforeach
                    </select></div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label">Fecha</label><input type="date" name="fecha" class="form-control" value="{{ old('fecha', date('Y-m-d')) }}" required></div>
                    <div class="col-6"><label class="form-label">Comprobante</label>
                        <select name="comprobante" class="form-select"><option>FACTURA</option><option>RECIBO</option><option>NOTA DE REMISION</option></select></div>
                </div>
                <label class="form-label">Total compra</label>
                <input class="form-control mb-3 text-end fw-bold" id="totalCampo" value="0.00" readonly>
                <button class="btn btn-nl w-100" id="btnRegistrar" disabled><i class="bi bi-save"></i> Registrar Compra</button>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalProd" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Buscar producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input id="filtroProd" class="form-control mb-3" placeholder="Escriba nombre o código...">
            <table class="table table-sm"><tbody id="listaProd"></tbody></table>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
var productos = @json($productos);
var items = [];
var cuerpo = document.getElementById('cuerpo');

function hallar(codigo) {
    codigo = codigo.trim().toLowerCase();
    return productos.find(function (p) { return p.codigo.toLowerCase() === codigo; });
}
function celda(tr, texto, clase) {
    var td = document.createElement('td');
    td.textContent = texto;
    if (clase) { td.className = clase; }
    tr.appendChild(td);
    return td;
}
function pintar() {
    cuerpo.innerHTML = '';
    var total = 0;
    if (items.length === 0) {
        cuerpo.innerHTML = '<tr id="vacio"><td colspan="9" class="text-center text-muted py-3"><i class="bi bi-info-circle"></i> No hay productos agregados</td></tr>';
    }
    items.forEach(function (it, i) {
        var tr = document.createElement('tr');
        var sub = it.cantidad * it.costo;
        total += sub;
        celda(tr, i + 1); celda(tr, it.codigo); celda(tr, it.cantidad); celda(tr, it.nombre); celda(tr, it.lote);
        celda(tr, it.venc || 'Sin fecha'); celda(tr, it.costo.toFixed(2), 'text-end'); celda(tr, sub.toFixed(2), 'text-end');
        var td = document.createElement('td');
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'btn btn-sm btn-outline-danger'; b.textContent = '×';
        b.addEventListener('click', function () { items.splice(i, 1); pintar(); });
        td.appendChild(b);
        var campos = { producto_id: it.id, cantidad: it.cantidad, costo: it.costo, lote: it.lote, vencimiento: it.venc || '' };
        Object.keys(campos).forEach(function (k) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = 'items[' + i + '][' + k + ']'; h.value = campos[k];
            td.appendChild(h);
        });
        tr.appendChild(td);
        cuerpo.appendChild(tr);
    });
    document.getElementById('nItems').textContent = items.length;
    document.getElementById('totalTxt').textContent = total.toFixed(2);
    document.getElementById('totalCampo').value = total.toFixed(2);
    document.getElementById('btnRegistrar').disabled = items.length === 0;
}
function agregar() {
    var p = hallar(document.getElementById('codigo').value);
    var cant = parseInt(document.getElementById('cant').value, 10) || 0;
    var costo = parseFloat(document.getElementById('costo').value) || 0;
    var lote = document.getElementById('lote').value.trim();
    var venc = document.getElementById('venc').value;
    if (!p) { alert('No se encontró un producto con ese código.'); return; }
    if (cant < 1) { alert('La cantidad debe ser mayor a 0.'); return; }
    if (!lote) { alert('Ingrese el número de lote.'); return; }
    items.push({ id: p.id, codigo: p.codigo, nombre: p.nombre, cantidad: cant, costo: costo, lote: lote, venc: venc });
    document.getElementById('codigo').value = '';
    document.getElementById('lote').value = '';
    document.getElementById('venc').value = '';
    document.getElementById('cant').value = 1;
    pintar();
}
document.getElementById('btnAgregar').addEventListener('click', agregar);
document.getElementById('codigo').addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); agregar(); } });

function listarProductos() {
    var f = document.getElementById('filtroProd').value.toLowerCase();
    var lista = document.getElementById('listaProd');
    lista.innerHTML = '';
    productos.filter(function (p) { return (p.codigo + ' ' + p.nombre).toLowerCase().indexOf(f) !== -1; }).forEach(function (p) {
        var tr = document.createElement('tr');
        celda(tr, p.codigo); celda(tr, p.nombre);
        var td = document.createElement('td');
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'btn btn-sm btn-nl'; b.textContent = 'Elegir';
        b.addEventListener('click', function () {
            document.getElementById('codigo').value = p.codigo;
            bootstrap.Modal.getInstance(document.getElementById('modalProd')).hide();
        });
        td.appendChild(b); tr.appendChild(td);
        lista.appendChild(tr);
    });
}
document.getElementById('filtroProd').addEventListener('input', listarProductos);
document.getElementById('modalProd').addEventListener('show.bs.modal', listarProductos);
document.getElementById('formCompra').addEventListener('submit', function (e) { if (items.length === 0) { e.preventDefault(); alert('Agregue al menos un producto.'); } });
</script>
@endpush
