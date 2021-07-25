<?php

use Illuminate\Database\Seeder;
use App\Table;

class MesaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->delete();
        $json = File::get("database/data-sample/mesa.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Table::create(array('id' => $obj->id, 'number' => $obj->numero, 'description' => $obj->descripcion));} 
    }
}
