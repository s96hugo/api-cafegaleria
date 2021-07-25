<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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

    public function delete(Request $req){
        Ticket::findOrFail($req->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'ticket deleted'
        ]);
    }

    //Añade la fecha a la que se realiza la cuenta
    //Calcula el precio total de todos los pedidos (no implementado)
    public function cuenta(Request $req){
        $ticketUpda = Ticket::findOrFail($req->id);
        $ticketUpda->date = date('Y/m/d H:i:s');
        //calcular total del ticket
        $ticketUpda->update();

        return response()->json([
            'success' => true,
            'messagge' => 'ticket closed',
            $ticketUpda
        ]);
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
        return response()->json([
            'success' => true,
            $tickets
        ]);
    }

    public function changeTable(Request $req){
        $tick = Ticket::findOrFail($req->id);
        $tick->table_id = $req->table_id;
        $tick->update();

        return response()->json([
            'success' => true,
            $tick
        ]);
    }

}
