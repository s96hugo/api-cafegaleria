<?php

namespace App\Http\Controllers\API;
use App\Product;
use App\Category;
use App\ProductOrder;
use App\Ticket;

use App\Http\Controllers\Controller;
use Dotenv\Result\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Create.
     * Crea un nuevo producto. Por facilidad para la implementación de la app, devuelve el 
     * producto con el nombre de la categoria al que pertenece.
     */
    public function create(Request $req){
        $newProd = new Product();
        $newProd->name = $req->name;
        $newProd->price = $req->price;
        $newProd->photo = $req->photo;
        $newProd->visible = true;
        $newProd->category_id = $req->category_id;
        $newProd->save();

        $mp = Product::select(
            'products.id', 
            'products.name',
            'products.price',
            'products.category_id',
            'categories.category'
        )    
        ->join('categories', 'categories.id', '=', 'products.category_id')
        ->where('products.visible', "=", true)
        ->where('products.id', '=', $newProd->id)
        ->get();

        return response()->json([
            'success' => true,
            'product' => $mp
        ]);
    }


    /**
     * get
     * devuelve un producto por id independientemente de si es visible o no.
     * Un producto se vuelve inisible cuando se borra (function delete) pero  ha sido
     * usado en algún pedido
     */
    public function get($id){
        $product = Product::findOrFail($id);
        return response()->json($product);
    }


    /**
     * getProductByCategoryId: 
     * No utilizado por la app
     * Trae trodos los productos visibles de una categoria (id)
     */
    public function getProductByCategoryId($id){
        $products = Product::all()->where('category_id', "=", $id)->where('visible', "=", true);
        return response()->json($products);
    }


    /**
     * mostPopular
     * Trae los 12 productos visibles más vendidos
     */
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


    /**
     * ProductCategory
     * Devuelve todos los productos visibles junto con el
     * nombre de la categoría al que pertenecen, y todas las categorias; para facilitar la implementación de la app.
     */
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

        $categories = Category::all();

        return response()->json([
            'success' => true,
            'products'=> $mp,
            'categories' => $categories
        ]);
    }


    /**
     * productDataSet
     * Devuelve toda la información necesaria para construir
     * la vista Order: Productos más vendidos, todos los productos, todas las 
     * categorias, todos los productos pedidos en cada una de las rondas (ProductOrder).
     * Creado por eficiencia, para hacerlo todo de 1 llamada a la API
     */
    public function productsDataSet($id){
        $ticket_id =$id;
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

        //Sacar toda la info de los pedidos de ese ticket
        $productOrderInfo = ProductOrder::select('product_orders.id', 'product_orders.units', 'product_orders.comment', 'products.name', 'product_orders.product_id', 'product_orders.order_id')
        ->join('orders', 'orders.id', '=', 'product_orders.order_id')
        ->join('tickets', 'orders.ticket_id', '=', 'tickets.id')
        ->join('products', 'products.id', '=', 'product_orders.product_id')
        ->where('tickets.id', '=', $ticket_id)->get();

        return response()->json([
            'success' => true,
            'mostPopular' => $mp,
            'products' => $pc,
            'categories' => $categories,
            'ticketOrderInfo' => $productOrderInfo
        ]);

    }


    /**
     * update
     * actualiza un producto y lo devuelve.
     */
    public function update($id, Request $req){
        $product = Product::findOrFail($id);
        $product->name = $req->name;
        $product->price = $req->price;
        $product->photo = $req->photo;
        $product->category_id = $req->category_id;
        $product->update();
        
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }


    /**
     * Delete
     * Método que borra un producto.
     * Primero comprobamos si hay al menos 1 pedido de ese producto:
     *   si NO HAY -> se borra
     *   SI HAY -> se invisibiliza
     */
    //
    public function delete($id){
        $mp = Product::select(
            'products.id'
        )    
        ->join('product_orders', 'products.id', '=', 'product_orders.product_id')
        ->where('products.visible', "=", true)
        ->where('products.id', '=', $id)
        ->take(1)
        ->get();

        $total = 0;
        foreach($mp as $product){
            $total += $product->id;
        }
        //Borrar
        if($total == 0){
            $product = Product::findOrFail($id);
            Product::findOrFail($id)->delete();
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        //Invisibilizar    
        } else{
            $product = Product::findOrFail($id);
            $product->visible = false;
            $product->update();

            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }
        
    }

    //Volver invisible. Un producto es invisible cuando se quiere borrar pero se ha usado previamente en un pedido
    public function invisible($id){
        $product = Product::findOrFail($id);
        $product->visible = false;
        $product->update();
        return response()->json([
            'success' => true
        ]);

    }

    //Volver visible
    public function visible($id){
        $product = Product::findOrFail($id);
        $product->visible = true;
        $product->update();
        return response()->json([
            'success' => true
        ]);

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
    

}


