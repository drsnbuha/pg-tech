<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ItemDetailsController extends Controller
{
    
    public function getItemDetails(Request $request)
    {
        $item_id = $request->input('item_id');

        $fieldList = "id, name, date, pcs_change, final_pcs, item_id";
        $condition = null;

        if ($item_id !== null) {
            $condition = "WHERE item_id = ?";
            $fieldList = "*";
        }

        $result = DB::select("SELECT $fieldList FROM item_details $condition", [$item_id]);

        if (count($result) > 0) {
            return response()->json([
                
                'data' => $result,
                'error' => 'no error'
            ]);
        } else {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }


    }
    

}
