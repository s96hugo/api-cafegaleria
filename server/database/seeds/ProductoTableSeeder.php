<?php

use Illuminate\Database\Seeder;
use App\Product;

class ProductoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->delete();
        $json = File::get("database/data-sample/producto.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Product::create(array('id' => $obj->id, 'name' => $obj->numbre, 'price' => $obj->precio, 'category_id' => $obj->Id_categoria ));}  

    }
}
