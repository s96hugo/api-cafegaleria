<?php

namespace App\Http\Controllers\API;
use App\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function create(Request $req){
        $newProd = new Product();
        $newProd->name = $req->name;
        $newProd->price = $req->price;
        $newProd->photo = $req->photo;
        $newProd->category_id = $req->category_id;
        $newProd->save();
        return response()->json($newProd);
    }

    public function getAll(){
        $products = Product::all();
        return response()->json($products);
    }

    public function get($id){
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update($id, Request $req){
        $product = Product::findOrFail($id);
        $product->name = $req->name;
        $product->price = $req->price;
        $product->photo = $req->photo;
        $product->category_id = $req->category_id;
        $product->update();
        return response()->json($product);
    }

    public function delete($id){
        $product = Product::findOrFail($id)->delete();
    }

    public function getProductByCategoryId($id){
        $products = Product::all()->where('category_id', "=", $id);
        return response()->json($products);
    }

    public function mostPopular(){
        $mp = Product::select(
            'products.id', 
            'products.name',
            DB::raw("(sum(product_orders.units)) as total")
        )    
        ->join('product_orders', 'product_orders.product_id', '=', 'products.id')
        ->orderBy('total', 'DESC')
        ->groupBy('products.id')
        ->take(3)
        ->get();


        return response()->json($mp);
    }
}
