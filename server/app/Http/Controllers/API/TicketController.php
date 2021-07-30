<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use Illuminate\Http\Request;
use App\Ticket;


class TicketController extends Controller
{
    //Método que crea un nuevo ticket, se inicializa con la mesa que se le manda por
    //parametro, y se crea un numero en funcion de la fecha. Posteriormente se le añade el
    // id al numero
    public function create(Request $req){
        $newTicket = new Ticket;
        $newTicket->number = (integer)str_replace("-","",date("Y-m-d"));
        $newTicket->table_id = $req->table_id;
        $newTicket->save();

        $numUnique = (integer)"$newTicket->number". $newTicket->id;;
        $newTicket->number = $numUnique;
        $newTicket->update();
        
        return response()->json(
            $newTicket
        );
    }

    public function delete($id){
        Ticket::findOrFail($id)->delete();
    }

    //Añade la fecha a la que se realiza la cuenta
    //Calcula el precio total de todos los pedidos (no implementado)
    public function cuenta($id){
        $ticketUpda = Ticket::findOrFail($id);
        $ticketUpda->date = date('Y/m/d H:i:s');
        $ticketUpda->total = $this->calcularTotal($id);
        $ticketUpda->update();

        return response()->json(
            $ticketUpda
        );
    }

    public function get(Request $req){
        $ticket = Ticket::findOrFail($req->id);
        return response()->json([
            'success' => true,
            $ticket
        ]);
    }

    public function getAll(){
        $tickets = Ticket::all();
        return response()->json(
            $tickets
        );
    }

    public function changeTable(Request $req){
        $tick = Ticket::findOrFail($req->id);
        $tick->table_id = $req->table_id;
        $tick->update();

        return response()->json(
            $tick
    );
    }

    public function calcularTotal(int $id){
        $prices = Ticket::select('products.price', 'product_orders.units')
        ->join('orders', 'orders.ticket_id', '=', 'tickets.id')
        ->join('product_orders', 'product_orders.order_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $id)->get();

        $total = 0;
        foreach($prices as $price){
            $total += $price->price * $price->units;
        }

        return $total;
    }

}
