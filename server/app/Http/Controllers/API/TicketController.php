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
        $tickets = Ticket::where('date', '!=', null)->orderBy('id', 'DESC')->take(100)->get();
        return response()->json([
            'success' => true,
            'tickets' => $tickets,
            'tables' => Table::all()
        ]);
    }

    //Función para cambiar de mesa un ticket. SI NO hay registro success false, token true.
    public function changeTable($id, Request $req){
        //Filtro1 -> Existe ticket activo en la mesa de origen
        $ticketf1 = Ticket::select('id')->where('date', '=', null)->where('table_id', '=', $id)->get();
        $ticket_id = 0;
        foreach($ticketf1 as $t){
            $ticket_id += $t->id;
        }

        if($ticket_id == 0){
            return response()->json([
                'success' => false,
                'token' => true
            ]);
        }

        //Filtro2 -> Mesa de destino vacía
        $ticketf2 = Ticket::select('id')->where('date', '=', null)->where('table_id', '=', $req->table_id)->get();
        $ticket_id2 = 0;
        foreach($ticketf2 as $t2){
            $ticket_id2 += $t2->id;
        }

        if($ticket_id2 != 0){
            return response()->json([
                'success' => false,
                'token' => true
            ]);
        }



        $tick = Ticket::findOrFail($ticket_id);
        $tick->table_id = $req->table_id;
        $tick->update();

        $tickets = Ticket::where('date', '=', null)->get();

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    //Función que calcula el total por "producto * unidades" a la hora de hacer la cuenta.
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
    }

    //Método que devuelve todos los productos, precio y unidades que se han pedidio en un ticket
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

    //Comprueba si existe un ticket activo en esa mesa, se utiliza a la hora de crear un ticket, para que no haya dos tickets en la misma mesa.
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

    //LLama a "CalcularUnidadesProducto" al igual que el método que realiza la cuenta. Este se usa para acceder a los productos de un ticket ya cerrado.
    public function showBill($id){
        $productsUnits = $this->calcularUnidadesProducto($id);
        return response()->json([
            'success' => true,
            'unidades' => $productsUnits
        ]);
    }


}
