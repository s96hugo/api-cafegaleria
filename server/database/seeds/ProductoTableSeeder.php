<?php

use Illuminate\Database\Seeder;
use App\Producto;

class ProductoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('productos')->delete();
        $json = File::get("database/data-sample/producto.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Producto::create(array('id' => $obj->id, 'nombre' => $obj->numbre, 'precio' => $obj->precio, 'Id_categoria' => $obj->Id_categoria ));}  

    }
}
