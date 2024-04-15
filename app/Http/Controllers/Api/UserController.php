<?php

namespace App\Http\Controllers\Api;

use stdClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $userTable = 'users';
        try {
        $validated = $request->validate([
            'user_name' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'business_name' => 'required|string',
            'phone_number' => 'required|string|unique:users',
            'gst_number' => 'required|string',
            'your_name' => 'required|string',
            'name' => 'required|string',
            'business_logo' => 'required|string',
            'signature' => 'required|string',
            'state' => 'required|string',
            'address' => 'required|string',
        ]);

        $user = new User([
            'user_name' => $validated['user_name'],
            'password' => Hash::make($request->input('password')),            
            'business_name' => $request->input('business_name'),
            'phone_number' => $request->input('phone_number'),
            'gst_number' => $request->input('gst_number'),
            'your_name' => $request->input('your_name'),
            'name' => $request->input('name'),
            'business_logo' => $request->input('business_logo'),
            'signature' => $request->input('signature'),
            'state' => $request->input('state'),
            'address' => $request->input('address'),
        ]);

        $user->save();

        return response()->json([
            'success' => true,
            'user'=> $user,
            'message' => 'User created successfully.',
        ]);
        } catch (ValidationException $exception) {

        $errorResponse = new stdClass();
        $errorResponse->success = false;
        $errorResponse->errors = [];

        foreach ($exception->errors() as $field => $errors) {
            $errorResponse->errors[$field] = $errors[0];
        }

        return response()->json($errorResponse, 400);
    }
}



public function userAddress(Request $request)
    {
        $state = $request->input('state');
        $address = $request->input('address');
        $id = $request->input('id');

        if (!$state || !$address || !$id) {
            return response()->json(['error' => 'Input(s) missing']);
        }

        $updateData = [
            'state' => $state,
            'address' => $address,
        ];

        DB::table('users')
            ->where('id', $id)
            ->update($updateData);

        $response = [
            'message' => 'Successfully updated state and address',
        ];

        return response()->json($response);
    }

    public function updateUser(Request $request)
    {
        $response = [];

        $id = $request->input('id');

        if (!$id) {
            return response()->json(['error' => 'Input(s) missing']);
        }

        $updates = [];

       
        $name = $request->input('name');
        if ($name !== null) {
            $updates['name'] = $name;
        }

        $gstNumber = $request->input('gst_number');
        if ($gstNumber !== null) {
            $updates['gst_number'] = $gstNumber;
        }

        
        $profile = $request->input('profile');
        if ($profile !== null) {
            $updates['profile'] = $profile;
        }

        
        $address = $request->input('address');
        if ($address !== null) {
            $updates['address'] = $address;
        }

        
        $state = $request->input('state');
        if ($state !== null) {
            $updates['state'] = $state;
        }

        
        if (empty($updates)) {
            return response()->json(['error' => 'No valid updates provided']);
        }

        DB::table('users')
            ->where('id', $id)
            ->update($updates);

        array_push($response, ['message' => 'User updated successfully']);

        return response()->json($response);
    }



    public function updatePhoneNumber(Request $request)
    {
        $response = [];

        $input = $request->all();

        if (!isset($input['phone_number'])) {
            return response()->json(['error' => 'Input(s) is missing']);
        } else {
            $id = $input['id'];
            $phone_number = $input['phone_number'];

            // Update the user's phone number using Eloquent or Query Builder
            // Replace 'YourModelName' with the actual model representing the 'user' table
            DB::table('users')->where('id', $id)->update([
                'phone_number' => $phone_number,
            ]);

            array_push($response, ["error" => "no error"]);
            array_push($response, ['message' => 'User updated successfully']);

            return response()->json($response);
        }
    }


    public function getSignature(Request $request)
    {
        $id = $request->input('id');
        $response = [];

        if (!$id) {
            return response()->json([
                'error' => 'missing_id',
                'message' => 'id parameter is required.',
            ]);
        }

        $signature = DB::table('users')
            ->select('signature')
            ->where('id', $id)
            ->first();

        if (!$signature) {
            return response()->json([
                'error' => 'no_data',
                'message' => 'Signature not found for the user with id ' . $id,
            ]);
        }

        return response()->json([
            'error' => 'no_error',
            'message' => 'Successfully retrieved user signature.',
            'data' => $signature,
        ]);
    }


    public function updateUserName(Request $request)
    {
        $response = [];

        
        $request->validate([
            'id' => 'required',
            'user_name' => 'required',
        ]);

        $id = $request->input('id');
        $userName = $request->input('user_name');

        
        DB::table('users')
            ->where('id', $id)
            ->update(['user_name' => $userName]);

        array_push($response, ["error" => "no error"]);
        array_push($response, ["message" => "Name successfully updated"]);

        return response()->json($response);
    }


    public function getUserData(Request $request)
    {
        $response = [];

       
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->input('id');
        $fieldList = [
            'business_name',
            'phone_number',
            'gst_number',
            'address',
            'state',
            'business_logo',
            'signature',
        ];

    
        $userData = DB::table('users')
            ->select($fieldList)
            ->where('id', $id)
            ->get();

        

       
        array_push($response, ["error" => "no error"]);
        
        
        $userData->each(function ($item) use (&$response) {
            array_push($response, $item);
        });

        return response()->json($response);
    }



    public function getBusiness(Request $request)
    {
        $response = [];

        
        $request->validate([
            'id' => 'required',
        ]);

        $id = $request->input('id');
        $fieldList = [
            'business_name',
            'business_logo',
        ];

        $userData = DB::table('users')
            ->select($fieldList)
            ->where('id', $id)
            ->get();

      

        
        array_push($response, ["error" => "no error"]);
        
        $userData->each(function ($item) use (&$response) {
            array_push($response, $item);
        });

        return response()->json($response);
    }


    


}

