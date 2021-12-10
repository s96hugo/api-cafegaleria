<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use Illuminate\Http\Request;
use App\Ticket;
use App\Table;
use Illuminate\Support\Facades\DB;


class TicketController extends Controller
{
    //Método que crea un nuevo ticket, se inicializa con la mesa que se le manda por
    //parametro, y se crea un numero en funcion de la fecha. Posteriormente se le añade el
    // id al numero
    public function create(Request $req){

        $check = $this->checkCurrentTicketAtTable($req->table_id);

        if($check != 0){
            return response()->json([
                'success' => false,
                'token' => true
            ]);

        } else {
            $newTicket = new Ticket;
            $newTicket->number = (integer)str_replace("-","",date("Y-m-d"));
            $newTicket->table_id = $req->table_id;
            $newTicket->save();

            $numUnique = (integer)"$newTicket->number". $newTicket->id;;
            $newTicket->number = $numUnique;
            $newTicket->update();
        
            return response()->json([
                'success' => true,
                'ticket' => $newTicket
            ]);
        }
    }

    public function delete($id){
        Ticket::findOrFail($id)->delete();
    }

    //Añade la fecha a la que se realiza la cuenta
    //TO Do: Al calcular el total, debe comrpobar si es 0, en ese caso se borra ese id de ticket y el success es false, pero token trve
    public function cuenta($id, Request $req){
        $ticketUpda = Ticket::findOrFail($id);
        $ticketUpda->date = date('Y/m/d H:i:s');
        $ticketUpda->total = $this->calcularTotal($id);
        $ticketUpda->payment = $this->setPago($req->pay);
        $ticketUpda->update();
        $productsUnits = $this->calcularUnidadesProducto($id);

        return response()->json([
            'success' => true,
            'ticket' => $ticketUpda,
            'unidades' => $productsUnits
        ]);
    }

    private function setPago(string $pay){
        if($pay == 0){ //efectivo
            return "en efectivo";
        } else if($pay == 1){ //Tarjeta 
            return "con tarjeta";
        } else {
            return "Impago";
        }
    }

    public function get(Request $req){
        $ticket = Ticket::findOrFail($req->id);
        return response()->json([
            'success' => true,
            $ticket
        ]);
    }

    public function getCurrentTickets(){
        $tickets = Ticket::where('date', '=', null)->get();
        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    public function getClosedTickets(){
        $tickets = Ticket::where('date', '!=', null)->orderBy('id', 'DESC')->get();
        return response()->json([
            'success' => true,
            'tickets' => $tickets,
            'tables' => Table::all()
        ]);
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

        $formattedNum = str_replace(',', '', $total);

        return $formattedNum;
        /*

        return number_format(
            $formattedNum,
            2
        );
        */
        
        
    }

    public function calcularUnidadesProducto($id){
        $productsUnits = Ticket::select('products.name',
                                'products.price', 
                                DB::raw("(sum(product_orders.units)) as unidades"))

        ->join('orders', 'orders.ticket_id', '=', 'tickets.id')
        ->join('product_orders', 'product_orders.order_id', '=', 'orders.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $id)
        ->groupBy('products.id')->get();

        return $productsUnits;
    }

    public function checkCurrentTicketAtTable($id){

        $ticketAtTable = Ticket::select(DB::raw("(count('tickets.number')) as units"))
        ->where('tickets.table_id', '=', $id)
        ->whereNull('tickets.date')
        ->groupBy('tickets.table_id')
        ->get();

        $total = 0;
        foreach($ticketAtTable as $price){
            $total += $price->units;
        }

    
        return $total;
    }

    public function showBill($id){
        $productsUnits = $this->calcularUnidadesProducto($id);
        return response()->json([
            'success' => true,
            'unidades' => $productsUnits
        ]);
    }


}
