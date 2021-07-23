<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'number',
        'description'
        
    ];

    public $timestamps = false;

    public function ticket(){
        return $this->hasMany('App\Ticket');
    }
}
