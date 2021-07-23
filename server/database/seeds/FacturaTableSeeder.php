<?php

use Illuminate\Database\Seeder;
use App\Factura;

class FacturaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('facturas')->delete();
        $json = File::get("database/data-sample/factura.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Factura::create(array('id' => $obj->id, 'numero' => $obj->numero, 'Id_mesa' => $obj->Id_mesa, 'fecha' => $obj->fecha ));}  

    }
}
