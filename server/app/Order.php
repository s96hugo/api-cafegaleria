<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id'
     ];

     public function productOrder(){
        return $this->hasMany('App\ProductOrder');
    }

    public function ticket(){
        return $this->belongsTo('App\Ticket');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
