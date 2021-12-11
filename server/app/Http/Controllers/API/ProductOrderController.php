<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductOrder;

class ProductOrderController extends Controller
{
    public function create(Request $req){
        $ord_id = 0;
        foreach($req->all() as $prod){
            $newProd = new ProductOrder();
            $newProd->units = $prod['units'];
            $newProd->comment = $prod['comment'] ?? '';
            $newProd->product_id = $prod['product_id'];
            $newProd->order_id = $prod['order_id'];
            $newProd->save();
            
            $ord_id = $prod['order_id'];
        }

        $productOrderInfo = $this->getAllProductsOrderTicket($ord_id);

        return response()->json([
            ['success' => true],
            ['ticketOrderInfo' => $productOrderInfo]
        ]);
    }

    public function getAll(){
        $productsOrders = ProductOrder::all();
        return response()->json($productsOrders);
    }

    public function update($id, Request $req){
        $prod = ProductOrder::findOrFail($id);
        $prod->units = $req->units;
        $prod->comment = $req->comment;
        $prod->product_id = $req->product_id;
        $prod->update();
        return response()->json($prod);
    }

    //Método que devuelve un json con todos los productos (y unidades) por cada order de ese ticket.
    public function getAllProductsOrderTicket($id){

        //Sacar el id ticket
        $ticket_id = ProductOrder::select('tickets.id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $id)->get();

        $total = 0;
        foreach($ticket_id as $ticket){
            $total = $ticket->id;
        }

        //Sacar toda la info de ese ticket
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $total)->get();

        return $productOrderInfo;

    }
}
