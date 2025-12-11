<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //Un pais tiene muchas provincias
    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}
