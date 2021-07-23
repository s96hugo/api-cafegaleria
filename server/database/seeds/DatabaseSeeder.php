<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(ProductoTableSeeder::class);
        $this->call(PedidoTableSeeder::class);
        $this->call(CategoriaTableSeeder::class);
        $this->call(FacturaTableSeeder::class);
        $this->call(MesaTableSeeder::class);
    }
}
