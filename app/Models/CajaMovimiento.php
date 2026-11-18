<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimiento extends Model
{
    protected $table = 'caja_movimientos';
    protected $guarded = [];

    protected $casts = ['fecha' => 'datetime'];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }
}
