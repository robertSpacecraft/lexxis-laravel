<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //Una Address pertenece a un User y una Address pertenece a una Street
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function street(){
        return $this->belongsTo(Street::class);
    }
}
