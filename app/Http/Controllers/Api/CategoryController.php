<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Middleware\Authenticate;


class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(Authenticate::class);
    }
    public function createCategory(Request $request){
        try {
            $request->validate([
                'category_name' => 'required|string|max:255|unique:category,category_name',
            ]);

            $userId = $request->user()->id;
            $categoryName = $request->input('category_name');

            DB::table('category')->insert([
                'user_id' => $userId,
                'category_name' => $categoryName
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category Added'
            ], 201);
        } catch (ValidationException $exception) {

            $errorResponse = new stdClass();
            $errorResponse->success = false;
            $errorResponse->errors = [];

            foreach ($exception->errors() as $field => $errors) {
                $errorResponse->errors[] = $errors[0];
            }

            $errorMessage = implode("\n", $errorResponse->errors);

            return response()->json([
                'success' => false,
                'error' => $errorMessage
            ], 400);

    }
}

public function index(Request $request)
{
    $id = $request->input('id');

    if (!$id) {
        return response()->json(['error' => 'Input Missing']);
    }

    $categoryData = DB::table('category')
        ->select('*')
        ->where('id', '=', $id)
        ->get();

    $response = [
        'total' => $categoryData->count(),
        'data' => $categoryData->toArray(),
        'error' => 'no error',
    ];

    return response()->json($response);
}


public function updateCategory(Request $request)
    {
        $response = [];

        $categoryName = $request->input('category_name');
        $id = $request->input('id');

        if (!$categoryName || !$id) {
            return response()->json(['error' => 'Input(s) missing']);
        }

        DB::table('category')
            ->where('id', $id)  
            ->update(['category_name' => $categoryName]);

        array_push($response, ['error' => 'no error']);
        array_push($response, ['message' => 'Successfully updated category']);

        return response()->json($response);
    }



    public function getUserCategories(Request $request)
    {
        $user_id = $request->input('user_id');
        $response = [];

        if (!$user_id) {
            return response()->json([
                'error' => 'missing_user_id',
                'message' => 'user_id parameter is required.',
            ]);
        }

        $categories = DB::table('category')
            ->select('id', 'category_name')
            ->where('user_id', $user_id)
            ->get();

        $count = $categories->count();

        if ($count > 0) {
            return response()->json([
                'error' => 'no_error',
                'message' => 'Successfully get user fieldlist data.',
                'total' => $count,
                'data' => $categories,
            ]);
        } else {
            return response()->json([
                'error' => 'no_data',
                'message' => 'No categories found for the user with id ' . $user_id,
            ]);
        }
    }

}
