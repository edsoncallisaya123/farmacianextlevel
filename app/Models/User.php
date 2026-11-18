<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    private $cachePermisos = null;

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol && $this->rol->nombre === 'admin';
    }

    public function tienePermiso(string $clave): bool
    {
        if (!$this->rol) {
            return false;
        }
        if ($this->esAdmin()) {
            return true;
        }
        if ($this->cachePermisos === null) {
            $this->cachePermisos = $this->rol->permisos()->pluck('clave')->all();
        }
        return in_array($clave, $this->cachePermisos, true);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->name . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
