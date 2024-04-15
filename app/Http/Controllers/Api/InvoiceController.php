<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function show_invoice(Request $request)
    {
        // Get the request parameters
        $id = $request->get('id');

        // Construct the SQL query
        $sql = "select id, customer_name, balance_amount, phone_number, cash_receive from invoice";
        if ($id !== null) {
            $sql .= " where id = $id";
        }

        // Execute the query and get the results
        $results = DB::select($sql);

        // Return the results as JSON
        return response()->json($results);
    }

    public function update_invoice($id, Request $request)
    {
        // Get the request parameters
        $customer_name = $request->get('customer_name');
        $phone_number = $request->get('phone_number');
        $cash_receive = $request->get('cash_receive');
        $balance_amount = $request->get('balance_amount');

        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'cash_receive' => 'required|numeric',
            'balance_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Update the invoice
        $sql = "update invoice set customer_name='$customer_name',phone_number='$phone_number',cash_receive='$cash_receive',balance_amount='$balance_amount' where id='$id' ";
        DB::update($sql);

        // Get the updated user details
        $invoice = DB::table('invoice')->where('id', $id)->first();

        // Return a success response with the updated user details
        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice,
        ]);
    }
    public function addItem(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'user_id' => 'required',
            'item_id' => 'required',
            'item_name' => 'required',
            'sales_price' => 'required',
            'total_sales_price' => 'required',
            'tax' => 'required',
            'total_tax' => 'required',
            'item_qty' => 'required',
            'total_item_qty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => 'no', 'message' => $validator->errors()->first()]);
        }

        DB::table('invoices')->insert([
            'user_id' => $input['user_id'],
            'item_id' => $input['item_id'],
            'item_name' => $input['item_name'],
            'sales_price' => $input['sales_price'],
            'total_sales_price' => $input['total_sales_price'],
            'tax' => $input['tax'],
            'total_tax' => $input['total_tax'],
            'item_qty' => $input['item_qty'],
            'total_item_qty' => $input['total_item_qty'],
        ]);

        return response()->json(['success' => 'yes', 'message' => 'Item Added']);
    }
//   $userId= $request->user()->id;



    public function index(Request $request)
    {
        $condition = null;
        $FieldList = "SUM(balance_amount) AS totalsum";

        if (isset($request->user_id)) {
            $condition = " where user_id='$request->user_id'";
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
    
    public function getAmountDetails(Request $request)
    {
        $condition = null;
        $FieldList = "balance_amount,total_amount";

        if (isset($request->user_id)) {
            $condition = " where balance_amount>0 && user_id='$request->user_id'";
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


    public function getUserBalance(Request $request)
    {
        $condition = null;
        $FieldList = "customer_name";

        if (isset($request->customer_name) || isset($request->user_id)) {
            $condition = " where customer_name='$request->customer_name' && user_id='$request->user_id'";
            $FieldList = "*";
        }

        $response = array();
        $sql = "SELECT SUM(balance_amount) AS totalsum FROM invoice $condition";
        $result = DB::select($sql);
        $count = count($result); //no of records fetch by select query
        array_push($response, array("total" => $count));
        foreach ($result as $row) {
            array_push($response, $row);
        }
        array_unshift($response, array("error" => "no error"));
        return response()->json($response);
    }

    public function updateFinalId(Request $request)
    {
        $response = [];
    
        $finalId = $request->input('final_id');
        $id = $request->input('id');
        $userId = $request->input('user_id');
    
        if (!$id || !$userId) {
            return response()->json(['error' => 'Input(s) missing']);
        }
    

        if ($finalId !== null) {
            DB::table('invoice_item_data')
                
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update(['final_id' => $finalId]);
        }
    
        $count = DB::table('invoice_item_data')
            ->where('final_id', $finalId);
            
    
        
        array_push($response, ['error' => 'no ']);
        array_push($response, ['message' => 'Successfully updated final_id']);
    
        return response()->json($response);
    }
    

    public function getUserInvoices(Request $request, $user_id)
    {
        $invoices = DB::table('invoice')
            ->where('user_id', $user_id)
            ->get();
    
        if ($invoices->isEmpty()) {
            return response()->json([
                'error' => 'no_data',
                'message' => 'No invoices found for the user with id ' . $user_id,
            ]);
        }
    
        return response()->json([
            'error' => 'no_error',
            'message' => 'Successfully retrieved invoices for the user with id ' . $user_id,
            'data' => $invoices,
        ]);
    }



    public function getInvoiceDetails(Request $request)
    {
        // Extract request parameters
        $id = $request->input('id');
        $customer_name = $request->input('customer_name');

        // Define the default field list and condition
        $fieldList = "id, customer_name, invoice_date, due_date, billing_address, gst_number, state, total_item, sub_total, additional_charge_name, additional_charge, discount, round_off, phone_number, total_amount, balance_amount, cash_receive";
        $condition = "";

        // Check if 'id' parameter is provided
        if ($id !== null) {
            // Check if 'customer_name' parameter is also provided
            if ($customer_name !== null) {
                $condition = " WHERE id = ? AND customer_name LIKE ?";
                $params = [$id, "%$customer_name%"];
            } else {
                $condition = " WHERE id = ?";
                $params = [$id];
            }
        }
        // Check if 'customer_name' parameter is provided
        else if ($customer_name !== null) {
            $condition = " WHERE customer_name LIKE ?";
            $params = ["%$customer_name%"];
        }

        // Query the database
        $result = DB::select("SELECT $fieldList FROM invoice $condition", $params);

        // Check if records were found
        if (count($result) > 0) {
            return response()->json([
                'total' => count($result),
                'data' => $result,
                'error' => 'no error'
            ]);
        } else {
            return response()->json([
                'error' => 'Data not found'
            ], 404); // HTTP 404 Not Found status code
        }
    }




    public function getBillInvoice(Request $request)
    {
       
        $id = $request->input('id');
        $customer_name = $request->input('customer_name');

       
        $fieldList = "id, item_id, customer_name, total_amount, due_date, invoice_date, balance_amount, total_amount, cash_receive";
        $condition = "";

       
        if ($id !== null) {
           
            if ($customer_name !== null) {
                $condition = " WHERE  id = ? AND customer_name LIKE ?";
                $params = [ $id, "%$customer_name%"];
            } else {
                $condition = "  id = ?";
                $params = [$id, $customer_name];
            }
        }

      
        $result = DB::select("SELECT $fieldList FROM invoice $condition", $params);

       
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



    public function updateInvoice(Request $request)
    {
        $id = $request->input('id', $request->query('id'));

        if ($id === null) {
            return response()->json([
                'error' => 'ID parameter is missing'
            ], 400);
        }

        $updateField = $request->input('update_field');
        $updateValue = $request->input($updateField);

        if ($updateField === null) {
            return response()->json([
                'error' => 'Update field is missing'
            ], 400);
        }

        $allowedFields = [
            'customer_name',
            'phone_number',
            'gst_number',
            'state',
            'billing_address',
            'additional_charge_name',
            'additional_charge',
            'discount_percentage',
            'discount',
            'total_amount',
            'cash_receive',
            'balance_amount',
            'due_date'
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

        DB::table('invoice')
            ->where('id', $id)
            ->update($dataToUpdate);

        return response()->json([
            'error' => 'no error',
            'message' => 'Invoice updated'
        ]);
    }

    
    
    

}
