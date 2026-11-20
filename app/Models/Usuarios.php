<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    // Adicione 'email' no array $fillable abaixo:
    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'telefone',
        'password',
        'is_admin',
    ];

    public function compras()
    {
        return $this->hasMany(Compras::class, 'id_usuario', 'id_usuario');
    }
}