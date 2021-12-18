<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product as ResourcesProduct;
use Illuminate\Http\Request;
use App\Ticket;
use App\Table;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;


class TicketController extends Controller
{
  
    /**
     * Create
     * Método que crea un nuevo ticket, se inicializa con la mesa que se le manda por
     * parametro, y se crea un numero en funcion de la fecha. Posteriormente se le añade el
     * id al numero
     * 
     */
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

    /**
     * Borra
     * Este método borra un ticket por id. Primero comprueba que seas admin (usuario con id = 1)
     * NOTA: En la app aún no hay ninguna llamada a este endpoint.
     * 
     */
    public function delete($id, Request $req){
        $userid = JWTAuth::toUser($req->token);
        if ($userid->id == 1) {
            Ticket::findOrFail($id)->delete();
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'token' => true
            ]);
        }
        
    }

    
    /**
     * Make Bill
     * Esta función añade la fecha a la que se realiza la cuenta,  llama al método que 
     * calcula el total de la cuenta, y establece el método de pago llamando al método setPago.
     * Se comprueban dos cosas: 
     * - Si el total a calcular es 0, en ese caso se borra el ticket. 
     * - Si ya hay una fecha en el ticket current (pòr id) significa que ya se le realizó esta operación previamente. 
     *  en ambos casos el success es false, pero token true.
     */
    public function cuenta($id, Request $req){
        $totalCuenta = $this->calcularTotal($id);   //El candidato a total de la cuenta calculado.
        $ticketUpda = Ticket::findOrFail($id);      //La fecha del ticket ya existente.
                
        if($totalCuenta == 0){
            Ticket::findOrFail($id)->delete();
            return response()->json([
                'success' => false,
                'token' => true
            ]);

        } else if ($ticketUpda->date != null) {
            return response()->json([
                'success' => false,
                'token' => true
            ]);

        } else {
            $ticketUpda->date = date('Y/m/d H:i:s');
            $ticketUpda->total = $totalCuenta;
            $ticketUpda->payment = $this->setPago($req->pay);
            $ticketUpda->update();
            $productsUnits = $this->calcularUnidadesProducto($id);

            return response()->json([
                'success' => true,
                'ticket' => $ticketUpda,
                'unidades' => $productsUnits
            ]);
        } 
    }


    /**
     * setPago
     * en función devuelve un string segun el valor que se le establezca
     */
    private function setPago(string $pay){
        if($pay == 0){ //efectivo
            return "en efectivo";
        } else if($pay == 1){ //Tarjeta 
            return "con tarjeta";
        } else {
            return "Impago";
        }
    }


    /**
     * get
     * devuelve un ticket por id
     */
    public function get(Request $req){
        $ticket = Ticket::findOrFail($req->id);
        return response()->json([
            'success' => true,
            $ticket
        ]);
    }


    /**
     * getCurrentTickets
     * Método que devuelve todos los tickets abiertos.
     */
    public function getCurrentTickets(){
        $tickets = Ticket::where('date', '=', null)->get();
        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }


    /**
     * getClosedTicket
     * Metodo que devuelve todos los tickets cerrados (con fecha) ,y 
     * a parte, también devuelve todas las mesas (para facilitar la implementación en la app)
     */
    public function getClosedTickets(){
        $tickets = Ticket::where('date', '!=', null)->orderBy('id', 'DESC')->take(500)->get();
        return response()->json([
            'success' => true,
            'tickets' => $tickets,
            'tables' => Table::all()
        ]);
    }


    /**
     * changeTable
     * Función que cambia un ticket activo de mesa.
     * Primero comprueba que en la mesa de origen haya un ticket activo, y segundo
     * comprueba que la mesa de destino esté vacía. SI NO hay registro success false, token true.
     * Un ticket se considera activo cuando no tiene fecha
     */
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

    /**
     * Calcular total
     * Función que se llama para calcular el total del ticket (llamado desde makeBill)
     * en el que se suma el valor de los productos multiplicado por las unidades pedidas
     */
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

    
    /**
     * CalcularUnidadesProducto
     * Método que devuelve todos los productos, precio y unidades que se han pedidio en un ticket.
     * Se llama desde showBill, para mostrar info de un ticket cerrado.
     */
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

    
    /**
     * showBill
     * LLama a "CalcularUnidadesProducto" al igual que el método que realiza la cuenta.
     * Este se usa para acceder a los productos de un ticket ya cerrado.
     */
    public function showBill($id){
        $productsUnits = $this->calcularUnidadesProducto($id);
        return response()->json([
            'success' => true,
            'unidades' => $productsUnits
        ]);
    }


}
