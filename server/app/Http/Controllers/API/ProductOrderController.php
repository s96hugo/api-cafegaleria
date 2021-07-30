<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProductOrder;

class ProductOrderController extends Controller
{
    public function create(Request $req){
        $newProd = new ProductOrder();
        $newProd->units = $req->units;
        $newProd->comment = $req->comment;
        $newProd->product_id = $req->product_id;
        $newProd->order_id = $req->order_id;
        $newProd->save();

        return response()->json($newProd);
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
}
