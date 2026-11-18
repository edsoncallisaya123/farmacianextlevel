<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $guarded = [];

    protected $casts = ['fecha' => 'date'];

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }
}
