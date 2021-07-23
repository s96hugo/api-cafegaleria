<?php

use Illuminate\Database\Seeder;
use App\Pedido;

class PedidoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pedidos')->delete();
        $json = File::get("database/data-sample/pedido.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            Pedido::create(array('id' => $obj->id, 'cantidad' => $obj->cantidad, 'fecha' => $obj->fecha, 'observacion' => $obj->observacion, 'Id_producto' => $obj->Id_producto, 'Id_factura' => $obj->Id_factura ));}  
    }
}
