<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductOrder;
use App\Order;
use App\Ticket;

class ProductOrderController extends Controller
{
    //
    /**
     * Create
     * Función que para cada elemento (productOrder) instancia un nuevo ProductOrder en la BBDD.
     * En la primera iteración crea un nuevo Order, ya que un productOrder tiene que tener un order asociado.
     * Posteriormente devuelve toda la info de ese ticket, a traves del método 'getAllProductsOrderTicket'.
     * Devuelve success false si el ticket ya se le realizó la cuenta (tiene fecha).
     */
    public function create(Request $req){

        $ticket_id = 0;                         //Condición. Buscamos ticket, si tiene fecha significa que el ticket está cerrado
        foreach($req->all() as $prod){          //No se permite hacer pedidos a un ticket con la cuenta realizada.
            $ticket_id = $prod['ticket_id'];
        }
        $ticketUpda = Ticket::findOrFail($ticket_id); 
        if ($ticketUpda->date != null) {
            return response()->json([
                ['success' => false],
                ['token' => true]
            ]);
        } 

        $ord_id = 0;
        foreach($req->all() as $prod){
            if($ord_id == 0){ //primera iteración, antes de crear el productOrder, se crea el Order que viene en la request
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
                $newProd->status = 1; //Pendiente
                $newProd->save();

            } else {//Demas iteraciones, se instacian los productsOrders
                $newProd = new ProductOrder();
                $newProd->units = $prod['units'];
                $newProd->comment = $prod['comment'] ?? '';
                $newProd->product_id = $prod['product_id'];
                $newProd->order_id = $ord_id;
                $newProd->status = 1;//pendiente-
                $newProd->save();
            }
        }

        $productOrderInfo = $this->getAllProductsOrderTicket($ord_id);

        return response()->json([
            ['success' => true],
            ['ticketOrderInfo' => $productOrderInfo]
        ]);
    }



    /**
     * Create
     * Función que un único productOrder.
     * Posteriormente devuelve toda la info de ese ticket, a traves del método 'getAllProductsOrderTicket'.
     * Devuelve success false si el ticket ya se le realizó la cuenta (tiene fecha).
     */
    public function crear(Request $req){
        $ticket_id = ProductOrder::select('tickets.id') //Conseguimos el id del ticket al que pertenece
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $req->order_id)->take(1)->get();

        foreach($ticket_id as $ticket){
            $ticket->id;
        }

        $ticketUpda = Ticket::findOrFail($ticket_id); 

        if ($ticketUpda[0]->date != null) {
            return response()->json([
                'success' => false,
                'token' => true
            ]);
        } else {

            $prod = new ProductOrder();
            $prod->units = $req->units;
            $prod->comment = $req->comment ?? '';
            $prod->product_id = $req->product_id;
            $prod->order_id = $req->order_id;
            $prod->status = 1;
            $prod->save();
            
            $productOrderInfo = $this->getAllProductsOrderTicket($req->order_id);

        return response()->json([
            'success' => true,
            'ticketOrderInfo' => $productOrderInfo
        ]);

        }
    }


    /**
     * getAll
     * Devuelve todos los products orders (no usado)
     */
    public function getAll(){
        $productsOrders = ProductOrder::all();
        return response()->json($productsOrders);
    }


    /**
     * update
     * Metodo que actualiza el valor de un pedido.
     * Primero comprueba que el ticket al que pertenece el pedido no este cerrado.
     * Se devuelve un json con el todos los pedidos de ese ticket ya actualizado.
     */
    public function update($id, Request $req){
        $ticket_id = ProductOrder::select('tickets.id') //Conseguimos el id del ticket al que pertenece
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $req->order_id)->take(1)->get();

        foreach($ticket_id as $ticket){
            $ticket->id;
        }

        
        $ticketUpda = Ticket::findOrFail($ticket_id); 
        //dd($ticketUpda);
        if ($ticketUpda[0]->date != null) { //Si la fecha no es nula, significa que se cerró.
            return response()->json([
                'success' => false,
                'token' => true
            ]);

        }  else {
            $prod = ProductOrder::findOrFail($id);
            $prod->units = $req->units;
            $prod->comment = $req->comment ?? "";
            $prod->product_id = $req->product_id;
            $prod->update();

            $info = $this->getAllProductsOrderTicket($prod->order_id);
            return response()->json([
                'success' => true,
                'ticketOrderInfo' => $info
            ]);
        }
    }


    /**
     * ticketProductOrderInfo
     * Metodo que devuelve toda la informacion necesaria para mostrar el estado del pedido
     * por rondas de un ticket (id).
     * Si no hay nada que mostrar (no se ha hecho ningun pedido devuelve success dlase token true)
     */
    public function ticketProductOrdersInfo($id){
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id', 'tables.description')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('tables', 'tables.id', '=', 'tickets.table_id')
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

    
    /**
     * getAllProductOrderTicket
     * Método que devuelve un informacion con todos los productos (y unidades) por cada order de ese ticket.
     * El parametro de entrada es un order_id.
     * Los endpoints que usan este método son: 'update', y 'create'
     * 
     */
    public function getAllProductsOrderTicket($id){

        //Sacar el id ticket
        $ticket_id = ProductOrder::select('tickets.id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $id)->take(1)->get();

        foreach($ticket_id as $ticket){
            $ticket->id;
        }

        //Sacar toda la info de ese ticket
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $ticket->id)
        ->orderBy('orders.id', 'ASC')
        ->get();

        return $productOrderInfo;
    }



    /**
     * delete
     * Método que elimina un productOrder.
     * El parametro de entrada es un order_id. y el id por url de PO.
     * Primero busca el ticket, si la fecha no es nula devuelva false, porque el ticket se cerro.
     * Si es el ultimo PO del order, en ese caso borra el order tb.
     *  
     */
    public function deleteProductOrder($id, Request $req){
        $ticket_id = ProductOrder::select('tickets.id') //Conseguimos el id del ticket al que pertenece
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->where('orders.id', '=', $req->order_id)->take(1)->get();

        foreach($ticket_id as $ticket){
            $ticket->id;
        }

        $ticketUpda = Ticket::findOrFail($ticket_id); 
        //dd($ticketUpda);
        if ($ticketUpda[0]->date != null) { //Si la fecha no es nula, significa que se cerró.
            return response()->json([
                'success' => false,
                'token' => true
            ]);

        } else {
            //sacamos la información del Order
            $order = Order::select('orders.id')->join('product_orders', 'product_orders.order_id', '=', 'orders.id')
                    ->where('orders.id', '=', $req->order_id);
            
            //CONDICION: si Order quedará vacío despues de borrar el productOrder -> borrar order
            if($order->count()==1){
                $prod = ProductOrder::findOrFail($id);
                ProductOrder::findOrFail($id)->delete();
                Order::findOrFail($req->order_id)->delete();
                return response()->json([
                    'success' => true,
                    'productOrder' => $prod,
                    'era el ultimo => true'
                ]);

            } else {
                $prod = ProductOrder::findOrFail($id);
                ProductOrder::findOrFail($id)->delete();
                return response()->json([
                    'success' => true,
                    'productOrder' => $prod,
                    'no era el ultimo' => true
                ]);
            }
        }
    }

    public function changeStatus($id, Request $req){
        $prod = ProductOrder::findOrFail($req->order_id);
        $prod->status = $id;
        $prod->update();

        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 
        'product_orders.comment', 'products.name', 'product_orders.product_id', 
        'product_orders.order_id', 'products.screenType', 'product_orders.status')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.date', '=', null)->get();
        return response()->json([
            'success' => true,
            'ticketOrderInfo' => $productOrderInfo
        ]);     
    }

}
