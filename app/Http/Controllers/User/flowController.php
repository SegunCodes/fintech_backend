<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Saving;
use App\Models\Transaction;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class flowController extends Controller
{
    public function personalSaving(Request $request){
        $rules = array(
            'savings_goal' => 'required',
            'frequency' => 'required',
            'target_amount' => 'required',
            'monthly_deposit' => 'required',
            'debit_day' => 'required',
            'start_date' => 'required',
            'stop_date' => 'required',
            'withdrawal_type' => 'required',
            'withdrawal_account_name' => 'required',
            'withdrawal_account_number' => 'required',
            'card_no' => 'required',
            'card_expiry_date' => 'required',
            'card_cvv' => 'required',
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
            $personalSaving = new Saving;
            $personalSaving->user_name = $loggedInUser->username;
            $personalSaving->savings_type = "personal savings";
            $personalSaving->savings_name = $request->input('savings_goal');
            $personalSaving->frequency = $request->input('frequency');
            $personalSaving->target_amount = $request->input('target_amount');
            $personalSaving->monthly_deposit = $request->input('monthly_deposit');
            $personalSaving->debit_day = $request->input('debit_day');
            $personalSaving->start_date = $request->input('start_date');
            $personalSaving->stop_date = $request->input('stop_date');
            $personalSaving->withdrawal_type = $request->input('withdrawal_type');
            $personalSaving->withdrawal_account_name = $request->input('withdrawal_account_name');
            $personalSaving->withdrawal_account_number = $request->input('withdrawal_account_number');
            $personalSaving->payment_method = 'debit card';
            $personalSaving->card_no = $request->input('card_no');
            $personalSaving->card_expiry_date = $request->input('card_expiry_date');
            $personalSaving->card_cvv = $request->input('card_cvv');
            $personalSaving->balance = '0.00';
            $save = $personalSaving->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }
    }
    public function topUP(Request $request){
        $loggedInUser = Auth::user();
        $newBalance = $loggedInUser->balance + $request->input('deposit');
        $updateBalance = User::where([
            'id'=> $loggedInUser->id
        ])->update([
            'balance' => $newBalance
        ]);
        if ($updateBalance) {
            $topUP = new Transaction;
            $topUP->user_name = $loggedInUser->username;
            $topUP->transaction_type = "deposit";
            $topUP->amount = $request->input('deposit');
            $topUP->account = "personal savings";
            $topUP->card = $request->input('card');
            $topUP->status = 'pending';
            $topUP->payment_method = 'debit card';
            $save = $topUP->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK, amount deposited"
                ], 200);
            }
        }else{
            return response()->json([
                "status_code"=> 422,
                "message" => "Could not process deposit"
            ], 422);
        }
    }
    public function fixedSaving(Request $request){
        $rules = array(
            'savings_goal' => 'required',
            'frequency' => 'required',
            'target_amount' => 'required',
            'monthly_deposit' => 'required',
            'debit_day' => 'required',
            'start_date' => 'required',
            'stop_date' => 'required',
            'withdrawal_type' => 'required',
            'withdrawal_account_name' => 'required',
            'withdrawal_account_number' => 'required',
            'card_no' => 'required',
            'card_expiry_date' => 'required',
            'card_cvv' => 'required',
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
            $fixedSaving = new Saving;
            $fixedSaving->user_name = $loggedInUser->username;
            $fixedSaving->savings_type = 'fixed saving';
            $fixedSaving->savings_name = $request->input('savings_goal');
            $fixedSaving->frequency = $request->input('frequency');
            $fixedSaving->target_amount = $request->input('target_amount');
            $fixedSaving->monthly_deposit = $request->input('monthly_deposit');
            $fixedSaving->debit_day = $request->input('debit_day');
            $fixedSaving->start_date = $request->input('start_date');
            $fixedSaving->stop_date = $request->input('stop_date');
            $fixedSaving->withdrawal_type = $request->input('withdrawal_type');
            $fixedSaving->withdrawal_account_name = $request->input('withdrawal_account_name');
            $fixedSaving->withdrawal_account_number = $request->input('withdrawal_account_number');
            $fixedSaving->payment_method = 'debit card';
            $fixedSaving->card_no = $request->input('card_no');
            $fixedSaving->card_expiry_date = $request->input('card_expiry_date');
            $fixedSaving->card_cvv = $request->input('card_cvv');
            $fixedSaving->balance = '0.00';
            $save = $fixedSaving->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }
    }
    public function withdrawal(Request $request){
        $rules = array(
            'amount' => 'required',
            'account_type' => 'required',
            'destination_acct_name' => 'required',
            'destination_acct_no' => 'required',
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
            $withdrawal = new Transaction;
            $withdrawal->user_name = $loggedInUser->username;
            $withdrawal->transaction_type = "withdrawal";
            $withdrawal->amount = $request->input('amount');
            $withdrawal->account = $request->input('account_type');
            $withdrawal->destination_acct_name = $request->input('destination_acct_name');
            $withdrawal->destination_acct_no = $request->input('destination_acct_no');
            $withdrawal->status = 'pending';
            $withdrawal->payment_method = 'cash transfer';
            $withdrawal->txn_reference = $withdrawal->id.hash('sha256', Str::random(15));
            $save = $withdrawal->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }
    }
    public function quickSaving(Request $request){
        $rules = array(
            'amount' => 'required',
            'account_type' => 'required',
            'card_no' => 'required',
            'card_expiry_date' => 'required',
            'card_cvv' => 'required',
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
            $quickSaving = new Transaction;
            $quickSaving->user_name = $loggedInUser->username;
            $quickSaving->transaction_type = "Deposit";
            $quickSaving->amount = $request->input('amount');
            $quickSaving->account = $request->input('account_type');
            $quickSaving->card_no = $request->input('card_no');
            $quickSaving->card_expiry_date = $request->input('card_expiry_date');
            $quickSaving->card_cvv = $request->input('card_cvv');
            $quickSaving->status = 'pending';
            $quickSaving->payment_method = 'debit card';
            $quickSaving->txn_reference = $quickSaving->id.hash('sha256', Str::random(15));
            $save = $quickSaving->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }
    }
}
