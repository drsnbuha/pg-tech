<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TempInvoiceController extends Controller
{
    public function addDataTemp(Request $request)
    {
        $response = [];

       
        $user_id = $request->input('user_id');
        $item_id = $request->input('item_id');
        $item_name = $request->input('item_name');
        $sales_price = $request->input('sales_price');
        $total_sales_price = $request->input('total_sales_price');
        $tax = $request->input('tax');
        $total_tax = $request->input('total_tax');
        $item_qty = $request->input('item_qty');
        $total_item_qty = $request->input('total_item_qty');
        $item_uuid = $request->input('item_uuid');

      
        if (
            $user_id === null ||
            $item_id === null ||
            $item_name === null ||
            $sales_price === null ||
            $total_sales_price === null ||
            $tax === null ||
            $total_tax === null ||
            $item_qty === null ||
            $total_item_qty === null ||
            $item_uuid === null
        ) {
            return response()->json(['error' => 'Input(s) missing'], 400);
        }

       
        DB::table('temp_invoice')->insert([
            'user_id' => $user_id,
            'item_id' => $item_id,
            'item_name' => $item_name,
            'sales_price' => $sales_price,
            'total_sales_price' => $total_sales_price,
            'tax' => $tax,
            'total_tax' => $total_tax,
            'item_qty' => $item_qty,
            'total_item_qty' => $total_item_qty,
            'item_uuid' => $item_uuid,
        ]);

        $response[] = ['success' => 'yes'];
        $response[] = ['message' => 'Item Added'];

        return response()->json($response, 200);
    }



    public function deleteItem(Request $request)
    {
        $response = [];

        $item_id = $request->input('item_id');
        
        if ($item_id === null) {
            return response()->json(['error' => 'item_id is missing'], 400);
        }

        
        $result = DB::table('temp_invoice')->where('item_id', $item_id)->delete();

        if ($result) {
            $response[] = ['error' => 'no error'];
            $response[] = ['message' => 'Item deleted'];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to delete item'], 500);
        }
    }

    public function getTotalItems(Request $request)
    {
        $response = [];

        
        $user_id = $request->input('user_id');

       
        if ($user_id === null) {
            return response()->json(['error' => 'user_id is missing'], 400);
        }

       
        $totalItemQty = DB::table('temp_invoice')
            ->where('user_id', $user_id)
            ->sum('item_qty');

        if ($totalItemQty !== null) {
            $response[] = ['error' => 'no error'];
            $response[] = ['total' => $totalItemQty];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to fetch total item_qty'], 500);
        }
    }


    public function getTotalItemQty(Request $request)
    {
        $response = [];

        
        $user_id = $request->input('user_id');

        
        if ($user_id === null) {
            return response()->json(['error' => 'user_id is missing'], 400);
        }

        
        $totalItemQty = DB::table('temp_invoice')
            ->where('user_id', $user_id)
            ->sum('total_item_qty');

        if ($totalItemQty !== null) {
            $response[] = ['error' => 'no error'];
            $response[] = ['total' => $totalItemQty];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to fetch total total_item_qty'], 500);
        }
    }


    public function getTotalSalesPrice(Request $request)
    {
        $response = [];

        $user_id = $request->input('user_id');

        if ($user_id === null) {
            return response()->json(['error' => 'user_id is missing'], 400);
        }

        $totalSalesPrice = DB::table('temp_invoice')
            ->where('user_id', $user_id)
            ->sum('total_sales_price');

        if ($totalSalesPrice !== null) {
            $response[] = ['error' => 'no error'];
            $response[] = ['total' => $totalSalesPrice];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to fetch total total_sales_price'], 500);
        }
    }

    public function updateTempInvoice(Request $request)
    {
        $response = [];

        $item_id = $request->input('item_id');
        $item_qty = $request->input('item_qty');
        $total_sales_price = $request->input('total_sales_price');
        $total_tax = $request->input('total_tax');

        if ($item_id === null) {
            return response()->json(['error' => 'item_id is missing'], 400);
        }

        $updateData = [];

        if ($item_qty !== null) {
            $updateData['item_qty'] = $item_qty;
        }

        if ($total_sales_price !== null) {
            $updateData['total_sales_price'] = $total_sales_price;
        }

        if ($total_tax !== null) {
            $updateData['total_tax'] = $total_tax;
        }

        if (empty($updateData)) {
            return response()->json(['error' => 'No fields to update'], 400);
        }

       
        $result = DB::table('temp_invoice')
            ->where('item_id', $item_id)
            ->update($updateData);

        if ($result) {
            $response[] = ['error' => 'no error'];
            $response[] = ['message' => 'Stock Updated'];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to update records'], 500);
        }
    }

    public function updateTempDetails(Request $request)
    {
        $response = [];

        $item_id = $request->input('item_id');
        $sales_price = $request->input('sales_price');
        $item_qty = $request->input('item_qty');
        $tax = $request->input('tax');
        $total_sales_price = $request->input('total_sales_price');
        $total_tax = $request->input('total_tax');

        if ($item_id === null) {
            return response()->json(['error' => 'item_id is missing'], 400);
        }

        $updateData = [];

        if ($sales_price !== null) {
            $updateData['sales_price'] = $sales_price;
        }

        if ($item_qty !== null) {
            $updateData['item_qty'] = $item_qty;
        }

        if ($tax !== null) {
            $updateData['tax'] = $tax;
        }

        if ($total_sales_price !== null) {
            $updateData['total_sales_price'] = $total_sales_price;
        }

        if ($total_tax !== null) {
            $updateData['total_tax'] = $total_tax;
        }

        if (empty($updateData)) {
            return response()->json(['error' => 'No fields to update'], 400);
        }

        $result = DB::table('temp_invoice')
            ->where('item_id', $item_id)
            ->update($updateData);

        if ($result) {
            $response[] = ['error' => 'no error'];
            $response[] = ['message' => 'Stock Updated'];
            return response()->json($response, 200);
        } else {
            return response()->json(['error' => 'Failed to update records'], 500);
        }
    }

    public function getTempInvoice(Request $request)
    {
        $response = [];

        $user_id = $request->input('user_id');
        $selectFields = $request->input('fields', '*'); 

        $fieldList = [
            'id',
            'item_id',
            'item_name',
            'sales_price',
            'tax',
            'total_sales_price',
            'total_tax',
            'item_qty',
            'total_item_qty',
        ];

        $query = DB::table('temp_invoice')
            ->select(DB::raw($selectFields))
            ->where('user_id', $user_id);

        $results = $query->get();

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

    public function getTempItemQty(Request $request)
    {
        $response = [];

        $item_id = $request->input('item_id');

        if ($item_id === null) {
            return response()->json(['error' => 'item_id is missing'], 400);
        }

        $query = DB::table('temp_invoice')
            ->select('item_id', 'item_qty')
            ->where('item_id', 'like', "$item_id%");

        $results = $query->get();

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

    public function getTempItem(Request $request)
    {
        $response = [];

        $item_id = $request->input('item_id');
        $item_name = $request->input('item_name');
        $selectFields = $request->input('fields', 'item_id,item_name,sales_price,tax,item_qty'); 

        $fieldList = explode(',', $selectFields);

        $query = DB::table('temp_invoice')->select($fieldList);

        if ($item_id !== null) {
            $query->where('item_id', $item_id);
        }

        if ($item_name !== null) {
            $query->where('item_name', 'like', "%$item_name%");
        }

        $results = $query->orderBy('item_id', 'desc')->get();

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
