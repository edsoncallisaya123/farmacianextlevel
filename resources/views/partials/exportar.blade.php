{{-- Botón "Exportar" (CSV / imprimir). Uso: @include('partials.exportar', ['tabla' => '#tablaX', 'nombre' => 'archivo']) --}}
<div class="dropdown d-inline-block no-imprimir">
    <button class="btn btn-nl-outline dropdown-toggle" data-bs-toggle="dropdown" type="button"><i class="bi bi-download"></i> Exportar</button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#" data-exportar="{{ $tabla }}" data-nombre="{{ $nombre ?? 'datos' }}"><i class="bi bi-filetype-csv"></i> Excel (CSV)</a></li>
        <li><a class="dropdown-item" href="#" onclick="window.print(); return false;"><i class="bi bi-printer"></i> Imprimir / PDF</a></li>
    </ul>
</div>
