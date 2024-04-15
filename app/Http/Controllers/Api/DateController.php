<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DateController extends Controller
{
    public function index(Request $request)
    {
        $condition = null;
        $FieldList = "summary_date";

        if (isset($request->summary_date) || isset($request->user_id)) {
            $condition = " where summary_date='$request->summary_date' && user_id='$request->user_id'";
            $FieldList = "*";
        }

        $response = [];
        $sql = "select $FieldList from invoice $condition ";
        $result = DB::select($sql);
        $count = count($result); //return no of record in result
        array_push($response, array("total" => $count));
        foreach ($result as $row) {
            array_push($response, $row);
        }
        array_unshift($response, array("error" => "no error"));
        return response()->json($response);
    }
}
