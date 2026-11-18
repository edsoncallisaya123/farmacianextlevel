@extends('layouts.app')
@section('titulo', 'Registrar nueva venta')
@section('migas', 'Ventas / Crear Venta')

@section('contenido')
<form method="post" action="{{ route('ventas.store') }}" id="formVenta">
    @csrf
    <input type="hidden" name="cliente_id" id="clienteId" value="{{ $clienteSel->id ?? '' }}">
    <div class="nl-card">
        <div class="nl-titulo"><h2>Registrar Nueva Venta</h2><a href="{{ route('ventas.index') }}" class="btn btn-nl-outline"><i class="bi bi-list"></i> Ver Historial</a></div>
    </div>
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="nl-card">
                <div class="nl-sub"><i class="bi bi-boxes"></i> Productos</div>
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-2"><label class="form-label">Cantidad</label><input type="number" id="cant" min="1" value="1" class="form-control"></div>
                    <div class="col-md-6">
                        <label class="form-label">Código</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-upc"></i></span><input id="codigo" class="form-control" placeholder="Ingresar código" autocomplete="off" autofocus></div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="button" class="btn btn-primary flex-fill" data-bs-toggle="modal" data-bs-target="#modalProd"><i class="bi bi-search"></i></button>
                        <button type="button" class="btn btn-success flex-fill" id="btnAgregar"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table tabla-items table-sm">
                        <thead><tr><th>#</th><th>Código</th><th>Cant.</th><th>Nombre</th><th class="text-end">Unit.</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                        <tbody id="cuerpo"><tr><td colspan="7" class="text-center text-muted py-3"><i class="bi bi-info-circle"></i> No hay productos agregados</td></tr></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between fw-bold border-top pt-2"><span>TOTAL: <span id="nItems">0</span></span><span>TOTAL VENTA: <span class="total-box">Bs <span id="totalTxt">0.00</span></span></span></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="nl-card">
                <div class="nl-sub"><i class="bi bi-person-lines-fill"></i> Datos del Cliente</div>
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-nl flex-fill" data-bs-toggle="modal" data-bs-target="#modalCli"><i class="bi bi-search"></i> Buscar Cliente</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-verde" title="Nuevo cliente"><i class="bi bi-plus-lg"></i> Nuevo</a>
                </div>
                <div class="mb-2"><label class="form-label">Nombre del Cliente</label><input class="form-control" id="cliNombre" value="{{ $clienteSel->nombre ?? 'S/N' }}" readonly></div>
                <div class="mb-2"><label class="form-label">NIT/CI</label><input class="form-control" id="cliNit" value="{{ $clienteSel->nit_ci ?? '0' }}" readonly></div>
                <div class="mb-2"><label class="form-label">Fecha de Venta</label><input class="form-control" value="{{ date('d/m/Y') }}" readonly></div>
                <label class="form-label">Total a Pagar</label>
                <input class="form-control mb-3 text-end fw-bold" id="totalCampo" value="Bs 0.00" readonly>
                <button class="btn btn-nl w-100" id="btnRegistrar" disabled><i class="bi bi-save"></i> Registrar Venta</button>
                <div class="small text-muted mt-2">Caja abierta desde {{ $caja->fecha_apertura->format('d/m/Y H:i') }}. El stock se descuenta del lote que vence primero.</div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="modalProd" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Buscar producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><input id="filtroProd" class="form-control mb-3" placeholder="Escriba nombre o código..."><table class="table table-sm"><tbody id="listaProd"></tbody></table></div>
    </div></div>
</div>
<div class="modal fade" id="modalCli" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Seleccionar cliente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><input id="filtroCli" class="form-control mb-3" placeholder="Nombre o NIT/CI..."><table class="table table-sm"><tbody id="listaCli"></tbody></table></div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
var productos = @json($productos);
var clientes = @json($clientes->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre, 'nit' => $c->nit_ci]));
var items = [];
var cuerpo = document.getElementById('cuerpo');

function celda(tr, texto, clase) {
    var td = document.createElement('td');
    td.textContent = texto;
    if (clase) { td.className = clase; }
    tr.appendChild(td);
    return td;
}
function hallar(codigo) {
    codigo = codigo.trim().toLowerCase();
    return productos.find(function (p) { return p.codigo.toLowerCase() === codigo; });
}
function pintar() {
    cuerpo.innerHTML = '';
    var total = 0;
    if (items.length === 0) {
        cuerpo.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3"><i class="bi bi-info-circle"></i> No hay productos agregados</td></tr>';
    }
    items.forEach(function (it, i) {
        var tr = document.createElement('tr');
        var sub = it.cantidad * it.precio;
        total += sub;
        celda(tr, i + 1); celda(tr, it.codigo); celda(tr, it.cantidad); celda(tr, it.nombre);
        celda(tr, it.precio.toFixed(2), 'text-end'); celda(tr, sub.toFixed(2), 'text-end');
        var td = document.createElement('td');
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'btn btn-sm btn-outline-danger'; b.textContent = '×';
        b.addEventListener('click', function () { items.splice(i, 1); pintar(); });
        td.appendChild(b);
        [['producto_id', it.id], ['cantidad', it.cantidad]].forEach(function (par) {
            var h = document.createElement('input');
            h.type = 'hidden'; h.name = 'items[' + i + '][' + par[0] + ']'; h.value = par[1];
            td.appendChild(h);
        });
        tr.appendChild(td);
        cuerpo.appendChild(tr);
    });
    document.getElementById('nItems').textContent = items.length;
    document.getElementById('totalTxt').textContent = total.toFixed(2);
    document.getElementById('totalCampo').value = 'Bs ' + total.toFixed(2);
    document.getElementById('btnRegistrar').disabled = items.length === 0;
}
function agregar() {
    var p = hallar(document.getElementById('codigo').value);
    var cant = parseInt(document.getElementById('cant').value, 10) || 0;
    if (!p) { alert('No se encontró un producto con ese código.'); return; }
    if (p.stock <= 0) { alert('El producto no tiene stock disponible.'); return; }
    if (cant < 1) { alert('La cantidad debe ser mayor a 0.'); return; }
    var existente = items.find(function (x) { return x.id === p.id; });
    var nuevaCant = (existente ? existente.cantidad : 0) + cant;
    if (nuevaCant > p.stock) { alert('Stock insuficiente. Disponible: ' + p.stock); return; }
    if (existente) { existente.cantidad = nuevaCant; }
    else { items.push({ id: p.id, codigo: p.codigo, nombre: p.nombre, precio: p.precio, cantidad: cant }); }
    document.getElementById('codigo').value = '';
    document.getElementById('cant').value = 1;
    document.getElementById('codigo').focus();
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
        celda(tr, p.codigo); celda(tr, p.nombre); celda(tr, 'Bs ' + p.precio.toFixed(2)); celda(tr, 'Stock ' + p.stock);
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

function listarClientes() {
    var f = document.getElementById('filtroCli').value.toLowerCase();
    var lista = document.getElementById('listaCli');
    lista.innerHTML = '';
    var general = document.createElement('tr');
    celda(general, 'Cliente general (S/N)'); celda(general, '');
    var tdg = document.createElement('td');
    var bg = document.createElement('button');
    bg.type = 'button'; bg.className = 'btn btn-sm btn-outline-secondary'; bg.textContent = 'Elegir';
    bg.addEventListener('click', function () { elegirCliente(null); });
    tdg.appendChild(bg); general.appendChild(tdg); lista.appendChild(general);
    clientes.filter(function (c) { return (c.nombre + ' ' + (c.nit || '')).toLowerCase().indexOf(f) !== -1; }).forEach(function (c) {
        var tr = document.createElement('tr');
        celda(tr, c.nombre); celda(tr, c.nit || '');
        var td = document.createElement('td');
        var b = document.createElement('button');
        b.type = 'button'; b.className = 'btn btn-sm btn-nl'; b.textContent = 'Elegir';
        b.addEventListener('click', function () { elegirCliente(c); });
        td.appendChild(b); tr.appendChild(td);
        lista.appendChild(tr);
    });
}
function elegirCliente(c) {
    document.getElementById('clienteId').value = c ? c.id : '';
    document.getElementById('cliNombre').value = c ? c.nombre : 'S/N';
    document.getElementById('cliNit').value = c ? (c.nit || '0') : '0';
    bootstrap.Modal.getInstance(document.getElementById('modalCli')).hide();
}
document.getElementById('filtroCli').addEventListener('input', listarClientes);
document.getElementById('modalCli').addEventListener('show.bs.modal', listarClientes);
document.getElementById('formVenta').addEventListener('submit', function (e) { if (items.length === 0) { e.preventDefault(); alert('Agregue al menos un producto.'); } });
</script>
@endpush
