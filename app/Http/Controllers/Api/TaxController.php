<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TaxController extends Controller
{
    public function total_tax(Request $request)
    {

        $finalId = $request->input('final_id');


        if (!$finalId) {
            return response()->json(['error' => 'Input Missing']);
        }


        $condition = "WHERE final_id = $finalId";

       
        $totalTax = DB::select("SELECT SUM(total_tax) AS totaltax FROM invoice_item_data $condition")[0]->totaltax;

        $totalQuantity = DB::select("SELECT SUM(item_qty) AS totalqty FROM invoice_item_data $condition")[0]->totalqty;



        $response = [
            'total' => $totalTax,
            'totalQuantity' => $totalQuantity,
        ];

        return response()->json($response);

    }
}
