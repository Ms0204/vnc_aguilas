<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'usuario_id',
        'recurso_id',
        'fecha_prestamo',
        'fecha_devolucion',
        'estado',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Relación con Recurso
    public function recurso()
    {
        return $this->belongsTo(Recurso::class, 'recurso_id');
    }
}
