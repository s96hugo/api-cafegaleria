<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Table;

class TableController extends Controller
{
    public function create(Request $req){
        $newTable = new Table;
        $newTable->number = $req->number;
        $newTable->description = $req->description;
        $newTable->save();

        return response()->json(
            $newTable
        );
    }

    public function getAll(){
        $listTables = Table::all();
        return response()->json(
            $listTables
        );
    }

    public function get($id){
        $table = Table::findOrFail($id);
        return response()->json(
            $table
        );
    }

    public function update($id, Request $req){
        $table = Table::findOrFail($id);
        $table->number = $req->number;
        $table->description = $req->description;
        $table->update();
        return response()->json(
            $table
        );
    }

    public function delete($id){
        Table::findOrFail($id)->delete();
    }
}
