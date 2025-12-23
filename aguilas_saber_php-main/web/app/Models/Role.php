<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',         // Nombre del rol (clave principal en Spatie)
        'guard_name',   // Guardia asociada ('web', 'api', etc.)
        'descripcion',  // Campo personalizado que usas en tu app
    ];
}