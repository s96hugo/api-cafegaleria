<?php

namespace App\Http\Controllers\API;
use App\Category;

use App\Http\Controllers\Controller;
use Dotenv\Result\Success;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $req){
        $newCategory = new Category();
        $newCategory->category = $req->category;
        $newCategory->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function getAll(){
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function get($id){
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update($id, Request $req){
        $category = Category::findOrFail($id);
        $category->category = $req->category;
        $category->update();
        return response()->json([
            'success' => true
        ]);
    }

    public function delete($id){
        Category::findOrFail($id)->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
