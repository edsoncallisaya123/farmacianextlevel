@extends('layouts.app')
@php $nuevo = !$producto->exists; @endphp
@section('titulo', $nuevo ? 'Nuevo producto' : 'Editar producto')
@section('migas', 'Productos')

@section('contenido')
<form method="post" action="{{ $nuevo ? route('productos.store') : route('productos.update', $producto) }}" enctype="multipart/form-data">
    @csrf
    @unless($nuevo) @method('PUT') @endunless
    <div class="nl-card sin-pad">
        <div class="p-3 d-flex justify-content-between align-items-center" style="background:var(--nl-navy);color:#fff">
            <div class="fs-5 fw-semibold"><i class="bi bi-plus-lg"></i> {{ $nuevo ? 'Nuevo Producto' : 'Editar Producto' }}</div>
            <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left"></i> Volver</a>
        </div>
        <div class="p-4">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Código <span class="text-danger">*</span></label><input name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo) }}" required></div>
                        <div class="col-md-8"><label class="form-label">Nombre <span class="text-danger">*</span></label><input name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" class="form-select" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $c)<option value="{{ $c->id }}" @selected(old('categoria_id', $producto->categoria_id) == $c->id)>{{ $c->nombre }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Laboratorio <span class="text-danger">*</span></label>
                            <select name="laboratorio_id" class="form-select" required>
                                <option value="">Seleccionar laboratorio</option>
                                @foreach($laboratorios as $l)<option value="{{ $l->id }}" @selected(old('laboratorio_id', $producto->laboratorio_id) == $l->id)>{{ $l->nombre }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion', $producto->descripcion) }}</textarea></div>
                        <div class="col-md-4"><label class="form-label">Precio de venta (Bs) <span class="text-danger">*</span></label><input type="number" step="0.01" min="0" name="precio_venta" class="form-control" value="{{ old('precio_venta', $producto->precio_venta) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Stock mínimo</label><input type="number" min="0" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $producto->stock_minimo) }}" required></div>
                        <div class="col-md-4"><label class="form-label">Stock máximo</label><input type="number" min="0" name="stock_maximo" class="form-control" value="{{ old('stock_maximo', $producto->stock_maximo) }}" required></div>
                        @unless($nuevo)
                        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" @checked(old('activo', $producto->activo))><label class="form-check-label" for="activo">Producto activo (visible en ventas y catálogo)</label></div></div>
                        @endunless
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-bold mb-3"><i class="bi bi-image"></i> Imagen del Producto</div>
                        <div class="border border-2 rounded p-3 text-center mb-3" style="border-style:dashed!important">
                            <input type="file" name="imagen" id="imagen" accept="image/png,image/jpeg" class="form-control">
                            <div class="small text-muted mt-2">Formatos: JPG, PNG (Máx. 2MB)</div>
                        </div>
                        <div class="border rounded p-3 text-center" style="min-height:170px">
                            <img id="vista" src="{{ $producto->imagen_url }}" alt="" style="max-width:100%;max-height:150px;{{ $producto->imagen_url ? '' : 'display:none' }}">
                            <div id="sinVista" @if($producto->imagen_url) style="display:none" @endif>
                                <i class="bi bi-image fs-1 text-primary"></i>
                                <div class="fw-semibold">Vista previa</div>
                                <div class="small text-muted">La imagen seleccionada aparecerá aquí</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-nl"><i class="bi bi-save"></i> Guardar producto</button>
                <a href="{{ route('productos.index') }}" class="btn btn-nl-outline">Cancelar</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('imagen').addEventListener('change', function (e) {
    var f = e.target.files[0];
    if (!f) { return; }
    var lector = new FileReader();
    lector.onload = function (ev) {
        var img = document.getElementById('vista');
        img.src = ev.target.result;
        img.style.display = 'inline-block';
        document.getElementById('sinVista').style.display = 'none';
    };
    lector.readAsDataURL(f);
});
</script>
@endpush
