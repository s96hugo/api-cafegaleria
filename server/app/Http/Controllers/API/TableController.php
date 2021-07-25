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

        return response()->json([
            'success' => true,
            'messagge' => 'table added',
            $newTable
        ]);
    }

    public function getAll(){
        $listTables = Table::all();
        return response()->json([
            'success' => true,
            $listTables
        ]);
    }

    public function get(Request $req){
        $table = Table::findOrFail($req->id);
        return response()->json([
            'success'=> true,
            'message'=> 'table update',
            $table
        ]);
    }

    public function update(Request $req){
        $table = Table::find($req->id);
        $table->number = $req->number;
        $table->description = $req->description;
        $table->update();
        return response()->json([
            'success'=> true,
            'message'=> 'table update',
            $table
        ]);
    }

    public function delete(Request $req){
        Table::findOrFail($req->id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'table deleted'
        ]);
    }
}
