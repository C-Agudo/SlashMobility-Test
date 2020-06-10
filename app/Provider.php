<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $table = 'provider';
    
    public function products(){
        return $this->hasMany('App\Product', 'provider_id');
    }
}
