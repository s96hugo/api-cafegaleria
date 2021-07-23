<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->dateTime('date', $precision = 0);
            $table->decimal('total', $precision = 8, $scale = 2);
            $table->unsignedBigInteger('table_id');
            $table->foreign('table_id')->references('id')->on('tables');
            $table->timestamps();
            
        });

        //NO PROBADO
        /*
        Schema::table('ticket', function(Blueprint $t) {
            // Add the Auto-Increment column
            $t->increments("number");
        
            // Remove the primary key
            $t->dropPrimary("number");
        
            // Set the actual primary key
            $t->primary(array("id"));
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
