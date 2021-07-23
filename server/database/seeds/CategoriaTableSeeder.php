<?php

use Illuminate\Database\Seeder;
use App\Categoria;

class CategoriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categorias')->delete();
        $json = File::get("database/data-sample/categoria.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Categoria::create(array('id' => $obj->id, 'categoria' => $obj->categoria));}  
    }
}
