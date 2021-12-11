<?php

namespace App\Http\Controllers\API;
use App\Product;
use App\Category;

use App\Http\Controllers\Controller;
use Dotenv\Result\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function create(Request $req){
        $newProd = new Product();
        $newProd->name = $req->name;
        $newProd->price = $req->price;
        $newProd->photo = $req->photo;
        $newProd->visible = true;
        $newProd->category_id = $req->category_id;
        $newProd->save();
        return response()->json([
            'success' => true
        ]);
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
        return response()->json([
            'success' => true
        ]);
    }

    public function delete($id){
        $product = Product::findOrFail($id)->delete();
    }

    public function invisible($id){
        $product = Product::findOrFail($id);
        $product->visible = false;
        $product->update();
        return response()->json([
            'success' => true
        ]);

    }

    public function visible($id){
        $product = Product::findOrFail($id);
        $product->visible = true;
        $product->update();
        return response()->json([
            'success' => true
        ]);

    }

    public function getProductByCategoryId($id){
        $products = Product::all()->where('category_id', "=", $id)->where('visible', "=", true);
        return response()->json($products);
    }

    public function mostPopular(){
        $mp = Product::select(
            'products.id', 
            'products.name',
            'products.price',
            'categories.category',
            DB::raw("(sum(product_orders.units)) as total")
        )    
        ->join('product_orders', 'product_orders.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->where('products.visible', "=", true)
        ->orderBy('total', 'DESC')
        ->groupBy('products.id')
        ->take(12)
        ->get();


        return response()->json([
            'success' => true,
            'mostPopular' => $mp
        ]);
    }

    public function productCategory(){
        $mp = Product::select(
            'products.id', 
            'products.name',
            'products.price',
            'products.category_id',
            'categories.category'
        )    
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->where('products.visible', "=", true)
        ->orderBy('products.category_id', 'ASC')
        ->get();


        return response()->json([
            'success' => true,
            'products'=> $mp]);
    }

    public function checkCategoryHasProduct($id){
        $products = Product::where('products.category_id', '=', $id)->get();
        $productsC = $products->count();
        if($productsC == 0){
            return response([
                'success' => true,
                'deleteable' => true
            ]);
        } else {
            return response([
                'success' => true,
                'deleteable' => false
            ]);
        } 

    }

    public function productsDataSet(){

        //Most popular
        $mp = Product::select(
            'products.id', 
            'products.name',
            'products.price',
            'categories.category',
            DB::raw("(sum(product_orders.units)) as total")
        )    
        ->join('product_orders', 'product_orders.product_id', '=', 'products.id')
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->where('products.visible', "=", true)
        ->orderBy('total', 'DESC')
        ->groupBy('products.id')
        ->take(12)
        ->get();

        //Products
        $pc = Product::select(
            'products.id', 
            'products.name',
            'products.price',
            'products.category_id',
            'categories.category'
        )    
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->where('products.visible', "=", true)
        ->orderBy('products.category_id', 'ASC')
        ->get();

        //Categories
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'mostPopular' => $mp,
            'products' => $pc,
            'categories' => $categories
        ]);

    }


}
