<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $guarded = [];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos()
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function getTotalIngresosAttribute(): float
    {
        return (float)$this->movimientos()->where('tipo', 'ingreso')->sum('monto');
    }

    public function getTotalEgresosAttribute(): float
    {
        return (float)$this->movimientos()->where('tipo', 'egreso')->sum('monto');
    }

    // Dinero que debería haber en caja: inicial + ingresos - egresos
    public function getMontoEsperadoAttribute(): float
    {
        return (float)$this->monto_inicial + $this->total_ingresos - $this->total_egresos;
    }
}
