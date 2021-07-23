<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $fillable = [
        'units',
        'comments',
        'product_id',
        'order_id'
     ];

     public $timestamps = false;

     public function product(){
        return $this->belongsTo('App\Product');
    }

    public function order(){
        return $this->belongsTo('App\Order');
    }
}
