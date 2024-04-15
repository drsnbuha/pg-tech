<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->input('id');

        $condition = '';
        $fieldList = 'id';

        if ($id) {
            $condition = "WHERE id = $id";
            $fieldList = '*';
        }

        $response = [];
        $sql = "SELECT $fieldList FROM item $condition";

        $stockData = DB::select($sql);

        $count = count($stockData);

        array_push($response, ["total" => $count]);

        foreach ($stockData as $row) {
            array_push($response, (array) $row);
        }

        array_unshift($response, ["error" => "no error"]);

        return response()->json($response);
    }



}
