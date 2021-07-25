<?php

use Illuminate\Database\Seeder;
use App\Category;

class CategoriaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();
        $json = File::get("database/data-sample/categoria.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Category::create(array('id' => $obj->id, 'category' => $obj->categoria));}  
    }
}
