<?php

namespace App;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'photo',
        'visible',
        'category_id'
     ];

     public $timestamps = false;

     public function category(){
        return $this->belongsTo('App\Category');
    }

    public function productOrder(){
        return $this->hasMany('App\ProductOrder');
    }
     
}
