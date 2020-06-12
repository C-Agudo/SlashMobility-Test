<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';

    public function provider(){
        return $this->belongsTo('App\Provider', 'provider_id');
    }
}
