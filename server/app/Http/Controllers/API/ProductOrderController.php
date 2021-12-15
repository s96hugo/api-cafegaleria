<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductOrder;
use App\Order;

class ProductOrderController extends Controller
{
    //Crea un order, que utiliza para crear el productOrder
    public function create(Request $req){
        $ord_id = 0;
        foreach($req->all() as $prod){
            //PRUEBA: fusion create order y productOrder
            if($ord_id == 0){
                $newOrder = new Order;
                $newOrder->ticket_id = $prod['ticket_id'];
                $newOrder->user_id = $prod['user_id'];
                $newOrder->save();

                $ord_id = $newOrder->id;

                $newProd = new ProductOrder();
                $newProd->units = $prod['units'];
                $newProd->comment = $prod['comment'] ?? '';
                $newProd->product_id = $prod['product_id'];
                $newProd->order_id = $ord_id;
                $newProd->save();

            } else {
                $newProd = new ProductOrder();
                $newProd->units = $prod['units'];
                $newProd->comment = $prod['comment'] ?? '';
                $newProd->product_id = $prod['product_id'];
                $newProd->order_id = $ord_id;//$prod['order_id'];
                $newProd->save();
            
                //$ord_id = $prod['order_id'];
            }
            
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

    public function ticketProductOrdersInfo($id){
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $id)->get();

        if($productOrderInfo->count() == 0){
            return response()->json([
                'success' => false,
                'token' => true
            ]);
        } else {
            return response()->json([
                'success' => true,
                'ticketOrderInfo' => $productOrderInfo
            ]);
        }

        
    }

    //Método que devuelve un json con todos los productos (y unidades) por cada order de ese ticket.
    public function getAllProductsOrderTicket($id){

        //Sacar el id ticket
        $ticket_id = ProductOrder::select('tickets.id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $id)->get();

        foreach($ticket_id as $ticket){
            $ticket->id;
        }

        //Sacar toda la info de ese ticket
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $ticket->id)->get();

        return $productOrderInfo;

    }
}
