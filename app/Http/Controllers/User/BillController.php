<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillController extends Controller
{
     //pay bills
    public function payAirtime(Request $request){
        $rules = array(
            'service_provider' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'password' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        if (!Hash::check($request->input('password'), $loggedInUser->password)) {
            return response()->json([
                "status_codes" => 400,
                "message"=> "incorrect password"
            ], 400);
        }else{
            $airtime = new Transaction;
            $airtime->user_name = $loggedInUser->username;
            $airtime->transaction_type = 'airtime';
            $airtime->service_provider = $request->input('service_provider');
            $airtime->phone = $request->input('phone');
            $airtime->amount = $request->input('amount');
            $airtime->payment_method = "debit card";
            $airtime->status = 'pending';
            $airtime->txn_reference = $airtime->id.hash('sha256', Str::random(15));
            $airtime->txn_charges = "";
            $save = $airtime->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }
    }
    public function payCable(Request $request){
        $rules = array(
            'service_provider' => 'required',
            'customer_id' => 'required',
            'service' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $cable = new Transaction;
        $cable->user_name = $loggedInUser->username;
        $cable->transaction_type = 'cable';
        $cable->service_provider = $request->input('service_provider');
        $cable->customer_id = $request->input('customer_id');
        $cable->service = $request->input('service');
        $cable->amount = $request->input('amount');
        $cable->payment_method = "debit card";
        $cable->status = 'pending';
        $cable->txn_reference = $cable->id.hash('sha256', Str::random(15));
        $cable->txn_charges = "";
        $save = $cable->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }
    }
    public function payElectricity(Request $request){
        $rules = array(
            'service_provider' => 'required',
            'customer_id' => 'required',
            'service' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $electricity = new Transaction;
        $electricity->user_name = $loggedInUser->username;
        $electricity->transaction_type = 'electricity';
        $electricity->service_provider = $request->input('service_provider');
        $electricity->customer_id = $request->input('customer_id');
        $electricity->service = $request->input('service');
        $electricity->amount = $request->input('amount');
        $electricity->payment_method = 'debit card';
        $electricity->status = 'pending';
        $electricity->txn_reference = $electricity->id.hash('sha256', Str::random(15));
        $electricity->txn_charges = "";
        $save = $electricity->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }
    }
    public function payInternet(Request $request){
        $rules = array(
            'service_provider' => 'required',
            'customer_id' => 'required',
            'service' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $internet = new Transaction;
        $internet->user_name = $loggedInUser->username;
        $internet->transaction_type = 'internet';
        $internet->service_provider = $request->input('service_provider');
        $internet->customer_id = $request->input('customer_id');
        $internet->service = $request->input('service');
        $internet->amount = $request->input('amount');
        $internet->payment_method = 'debit card';
        $internet->status = 'pending';
        $internet->txn_reference = $internet->id.hash('sha256', Str::random(15));
        $internet->txn_charges = "";
        $save = $internet->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }
    }
    public function payData(Request $request){
        $rules = array(
            'service_provider' => 'required',
            'phone' => 'required',
            'data' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $data = new Transaction;
        $data->user_name = $loggedInUser->username;
        $data->transaction_type = 'data';
        $data->service_provider = $request->input('service_provider');
        $data->phone = $request->input('phone');
        $data->data = $request->input('data');
        $data->amount = $request->input('amount');
        $data->payment_method = "debit card";
        $data->status = 'pending';
        $data->txn_reference = $data->id.hash('sha256', Str::random(15));
        $data->txn_charges = "";
        $save = $data->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }       
    }
}
