<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Inspector extends Authenticatable
{
    use Notifiable;

    protected $table = 'fa_inspector';
    
    // Si la tabla no usa timestamps (created_at, updated_at)
    public $timestamps = false;
    
    // Si tu primary key no es 'id', especifícala aquí
    // protected $primaryKey = 'id_inspector'; // Descomenta si es necesario
    
    protected $fillable = [
        'dni',
        'password',
        'nombre',
        'apellido',
        'dto_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Si en tu tabla el campo de password tiene otro nombre,
     * especifícalo aquí
     */
    public function getAuthPassword()
    {
        return $this->password;
    }   

    public function username()
    {
        return 'dni';
    }
}