<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PartyController extends Controller
{
    public function getUserParties(Request $request)
    {
        $user_id = $request->input('user_id');
        $response = [];

        if (!$user_id) {
            return response()->json([
                'error' => 'missing_user_id',
                'message' => 'user_id parameter is required.',
            ]);
        }

        $parties = DB::table('party')
            ->select('party_name')
            ->where('user_id', $user_id)
            ->get();

        $count = $parties->count();

        if ($count > 0) {
            return response()->json([
                'error' => 'no_error',
                'message' => 'Successfully retrieved user parties data.',
                'total' => $count,
                'data' => $parties,
            ]);
        } else {
            return response()->json([
                'error' => 'no_data',
                'message' => 'No parties found for the user with id ' . $user_id,
            ]);
        }
    }


    public function removeUserDetails(Request $request)
    {
       
        $party_name = $request->input('party_name');
        $user_id = $request->input('user_id');

        
        if ($party_name === null || $user_id === null) {
            return response()->json([
                'error' => 'Input(s) missing'
            ], 400); 
        }

       
        DB::table('party')
            ->where('party_name', $party_name)
            ->where('user_id', $user_id)
            ->delete();

        return response()->json([
            'error' => 'no error',
            'message' => 'Party deleted'
        ]);
    }

}
