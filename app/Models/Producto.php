<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $guarded = [];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class);
    }

    // Agrega el stock vigente (lotes no vencidos) y el próximo vencimiento
    public function scopeConStock($query)
    {
        return $query
            ->withSum(['lotes as stock_vigente' => function ($l) {
                $l->vigentes();
            }], 'cantidad_actual')
            ->withMin(['lotes as proximo_vencimiento' => function ($l) {
                $l->where('cantidad_actual', '>', 0);
            }], 'fecha_vencimiento');
    }

    public function getStockAttribute(): int
    {
        if (array_key_exists('stock_vigente', $this->attributes)) {
            return (int)$this->attributes['stock_vigente'];
        }
        return (int)$this->lotes()->vigentes()->sum('cantidad_actual');
    }

    public function getImagenUrlAttribute(): ?string
    {
        if ($this->imagen) {
            return asset('storage/' . $this->imagen);
        }
        // Si existe una foto llamada como el código (P001.jpg) en public/assets/productos, se usa esa
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $archivo = 'assets/productos/' . $this->codigo . '.' . $ext;
            if (file_exists(public_path($archivo))) {
                return asset($archivo);
            }
        }
        return null;
    }

    // Normal | Precaución | Crítico | Agotado
    public function getEstadoStockAttribute(): string
    {
        $s = $this->stock;
        if ($s <= 0) {
            return 'Agotado';
        }
        if ($this->stock_minimo > 0 && $s <= intdiv($this->stock_minimo, 2)) {
            return 'Crítico';
        }
        if ($s < $this->stock_minimo) {
            return 'Precaución';
        }
        return 'Normal';
    }
}
