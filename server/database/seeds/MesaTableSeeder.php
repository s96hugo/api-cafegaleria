<?php

use Illuminate\Database\Seeder;
use App\Mesa;

class MesaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mesas')->delete();
        $json = File::get("database/data-sample/mesa.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Mesa::create(array('id' => $obj->id, 'numero' => $obj->numero, 'descripcion' => $obj->descripcion));} 
    }
}
