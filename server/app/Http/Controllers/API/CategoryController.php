<?php

namespace App\Http\Controllers\API;
use App\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $req){
        $newCategory = new Category();
        $newCategory->category = $req->category;
        $newCategory->save();
        return response()->json($newCategory);
    }

    public function getAll(){
        $categories = Category::all();
        return response()->json($categories);
    }

    public function get($id){
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update($id, Request $req){
        $category = Category::findOrFail($id);
        $category->category = $req->category;
        $category->update();
        return response()->json($category);
    }

    public function delete($id){
        Category::findOrFail($id)->delete();
    }
}
