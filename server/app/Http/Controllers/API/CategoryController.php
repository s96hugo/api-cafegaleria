<?php

namespace App\Http\Controllers\API;
use App\Category;
use App\Product;

use App\Http\Controllers\Controller;
use Dotenv\Result\Success;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * Crear
     * Crea una categoría. La devuelve
     */
    public function create(Request $req){
        $newCategory = new Category();
        $newCategory->category = $req->category;
        $newCategory->save();

        $categories = Category::all();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Devuelve todas las categorías.
     */
    public function getAll(){
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Devuelve 1 categoría.
     */
    public function get($id){
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update
     * Actualiza el nombre de una categoría y la devuelve.
     */
    public function update($id, Request $req){
        $category = Category::findOrFail($id);
        $category->category = $req->category;
        $category->update();

        $categories = Category::all();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Borrar
     * Funcion que borra una categoria. Primero comprueba que no haya
     * ningún producto asociado a esa categoria, sea visible o invisible.
     * si NO HAY -> borra la categoría y la devuelve via json.
     * SI HAY -> No hace nada, devuelve false success.
     */
    public function delete($id){
        $products = Product::where('products.category_id', '=', $id)->get();
        $productsC = $products->count();
        if($productsC == 0){
            $cat = Category::findOrFail($id);
            Category::findOrFail($id)->delete();
            return response([
                'success' => true,
                'category' => $cat
            ]);
        } else {
            return response([
                'success' => false,
                'token' => true
            ]);
        } 
    }
}
