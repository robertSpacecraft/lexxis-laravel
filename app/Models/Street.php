<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    //Una Street pertenece a una City y una Street tiene muchas Address
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }
}
