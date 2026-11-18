<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    // Lotes con stock y que no están vencidos (o que no tienen fecha de vencimiento)
    public function scopeVigentes($query)
    {
        return $query->where('cantidad_actual', '>', 0)
            ->where(function ($w) {
                $w->whereNull('fecha_vencimiento')
                  ->orWhere('fecha_vencimiento', '>=', Carbon::today()->toDateString());
            });
    }

    public function getEstadoAttribute(): string
    {
        if ($this->cantidad_actual <= 0) {
            return 'Agotado';
        }
        if (!$this->fecha_vencimiento) {
            return 'Activo';
        }
        if ($this->fecha_vencimiento->lt(Carbon::today())) {
            return 'Vencido';
        }
        if ($this->fecha_vencimiento->lte(Carbon::today()->addDays(30))) {
            return 'Por vencer';
        }
        return 'Activo';
    }
}
