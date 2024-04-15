<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class AddItemController extends Controller
{

        public function addItem(Request $request)
    {
        // Get the item table name.
        $itemTable = 'item';

        // Validate the request.
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|string|max:255|unique:item,item_name',
            'sales_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'msn' => 'required|numeric',
            'gst' => 'required|string',
            'opening_stock' => 'required|numeric',
            'item_date' => 'required|string',
            'item_image' => 'required|string',
            'item_category' => 'required|string',
            'item_remark' => 'required|string',
            's_price_add_gst' => 'required|string',
            'p_price_add_gst' => 'required|string',
            'low_stock_warning' => 'required|string',
            'temp_stock' => 'required|numeric',
            'extra_qty' => 'required|numeric',
        ]);

        // Return an error response if the validation fails.
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        // Get the user ID and item data from the request.
        $userId = $request->user()->id;
        $item_name = $request->input('item_name');
        $sales_price = $request->input('sales_price');
        $purchase_price = $request->input('purchase_price');
        $msn = $request->input('msn');
        $gst = $request->input('gst');
        $opening_stock = $request->input('opening_stock');
        $item_date = $request->input('item_date');
        $item_image = $request->input('item_image');
        $item_category = $request->input('item_category');
        $item_remark = $request->input('item_remark');
        $s_price_add_gst = $request->input('s_price_add_gst');
        $p_price_add_gst = $request->input('p_price_add_gst');
        $low_stock_warning = $request->input('low_stock_warning');
        $temp_stock = $request->input('temp_stock');
        $extra_qty = $request->input('extra_qty');

        // Insert the item data into the database.
        DB::table($itemTable)->insert([
            'user_id' => $userId,
            'item_name' => $item_name,
            'sales_price' => $sales_price,
            'purchase_price' => $purchase_price,
            'msn' => $msn,
            'gst' => $gst,
            'opening_stock' => $opening_stock,
            'item_date' => $item_date,
            'item_image' => $item_image,
            'item_category' => $item_category,
            'item_remark' => $item_remark,
            's_price_add_gst' => $s_price_add_gst,
            'p_price_add_gst' => $p_price_add_gst,
            'low_stock_warning' => $low_stock_warning,
            'temp_stock' => $temp_stock,
            'extra_qty' => $extra_qty
        ]);

        // Return a success response.
        return response()->json([
            'success' => true,
            'message' => 'Item Added',
        ]);
    }

    // add item details API
    public function addItemDetail(Request $request){

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|numeric',
            'name' => 'required|string|unique:item_details,name',
            'date' => 'required|string',
            'pcs_change' => 'required|string',
            'final_pcs' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $userId= $request->user()->id;
        $item_id = $request->input('item_id');
        $name = $request->input('name');
        $date = $request->input('date');
        $pcsChange = $request->input('pcs_change');
        $finalPcs = $request->input('final_pcs');

        DB::table('item_details')->insert([
            'user_id' => $userId,
            'item_id' => $item_id,
            'name' => $name,
            'date' => $date,
            'pcs_change' => $pcsChange,
            'final_pcs' => $finalPcs
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item added successfully'
        ], 201);
    }

    public function updateExtraQuantity(Request $request)
    {
        $response = [];

        $extraQty = $request->input('extra_qty');
        $id = $request->input('id');

        if (!$extraQty || !$id) {
            return response()->json(['error' => 'Input(s) missing']);
        }

        DB::table('item')
            ->where('id', $id)
            ->update(['extra_qty' => $extraQty]);

        array_push($response, ['error' => 'no error']);
        array_push($response, ['message' => 'Successfully updated extra quantity']);

        return response()->json($response);
    }


    public function updateOpeningStock(Request $request)
    {
        $response = [];

        $openingStock = $request->input('opening_stock');
        $id = $request->input('id');

        if (!$openingStock || !$id) {
            return response()->json(['error' => 'Input(s) missing']);
        }

        // Update opening_stock in the database
        DB::table('item')
            ->where('id', $id)
            ->update(['opening_stock' => $openingStock]);

        $message = 'Stock Updated';

        array_push($response, ['error' => 'no error']);
        array_push($response, ['message' => $message]);

        return response()->json($response);
    }




    public function getItemList(Request $request)
    {
        
        $user_id = $request->input('user_id');

       
        $fieldList = "id, item_name, sales_price, gst, s_price_add_gst, p_price_add_gst, opening_stock, item_image, item_remark, low_stock_warning, extra_qty";
        $condition = null;

       
        if ($user_id !== null) {
            $condition = " WHERE user_id = ?";
            $fieldList = "*";
        }

       
        $result = DB::select("SELECT $fieldList FROM item $condition", [$user_id]);

       
        if (count($result) > 0) {
            return response()->json([
                'total' => count($result),
                'data' => $result,
                'error' => 'no error'
            ]);
        } else {
            return response()->json([
                'error' => 'Data not found'
            ], 404);
        }
    }


    public function updateItem(Request $request)
    {
        $id = $request->input('id', $request->query('id'));

        if ($id === null) {
            return response()->json([
                'error' => 'ID parameter is missing'
            ], 400);
        }

        $updateField = $request->input('update_item');
        $updateValue = $request->input($updateField);

        if ($updateField === null) {
            return response()->json([
                'error' => 'Update field is missing'
            ], 400);
        }

        $allowedFields = [
            'item_name',
            'sales_price',
            'purchase_price',
            'msn',
            'gst',
            'opening_stock',
            'item_date',
            'item_image',
            'item_category',
            'item_remark',
            's_price_add_gst',
            'p_price_add_gst',
            'low_stock_warning',
            'temp_stock'
        ];

        if ($updateField !== 'all' && !in_array($updateField, $allowedFields)) {
            return response()->json([
                'error' => 'Invalid update field'
            ], 400);
        }

        $dataToUpdate = [];

        if ($updateField === 'all') {
        
            foreach ($allowedFields as $field) {
                $dataToUpdate[$field] = $request->input($field);
            }
        } else {
        
            $dataToUpdate[$updateField] = $updateValue;
        }

        DB::table('item')
            ->where('id', $id)
            ->update($dataToUpdate);

        return response()->json([
            'error' => 'no error',
            'message' => 'Invoice updated'
        ]);
    }

    public function dateItem(Request $request)
    {
        $response = [];

        $id = $request->input('id');
        $item_date = $request->input('item_date');

        $fieldList = ['id', 'item_date', 'temp_stock'];

        $query = DB::table('item')->select($fieldList);

        if ($id !== null) {
            $query->orWhere('id', $id);
        }

        if ($item_date !== null) {
            $query->orWhere('item_date', 'like', "%$item_date%");
        }

        $results = $query->orderBy('id', 'desc')->get();

        $count = count($results);

        if ($count > 0) {
            $response[] = ['total' => $count];
            foreach ($results as $result) {
                $response[] = (array) $result;
            }
            $response[] = ['error' => 'no error'];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'No records found'], 404);
        }
    }


    }

