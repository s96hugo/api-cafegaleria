<?php

namespace App;

use App\product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category'
    ];

    public $timestamps = false;

    public function product(){
        return $this->hasMany('App\Product');
    }
}
