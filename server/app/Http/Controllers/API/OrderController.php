<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;

class OrderController extends Controller
{
    public function create(Request $req){
        $newOrder = new Order;
        $newOrder->ticket_id = $req->ticket_id;
        $newOrder->user_id = $req->user_id;
        $newOrder->save();

        return response()->json([
            'success' => true,
            'order' => $newOrder
        ]);
    }

    public function getAll(){
        $orders = Order::all();
        return response()->json($orders);
    }
    public function delete($id){
        Order::findOrFail($id)->delete();
    }

}
