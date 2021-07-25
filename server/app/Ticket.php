<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'number',
        'date',
        'total',
        'table_id'
    ];

    public $timestamps = false;

    public function table(){
        return $this->belongsTo('App\Table');
    }

    public function order(){
        return $this->hasMany('App\Order');
    }
}
