<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //Una City pertenece a una Province y una City tiene muchas Street
    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function streets(){
        return $this->hasMany(Street::class);
    }
}
