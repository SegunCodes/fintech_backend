<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Investment;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    public function investmentSetUp(Request $request){
        $loggedInUser = Auth::user();
        $investmentSetUp = new Investment;
        $investmentSetUp->user_name = $loggedInUser->username;
        $investmentSetUp->investment_type = "investment";
        $investmentSetUp->investment_title = $request->input('investment_title');
        $investmentSetUp->frequency = $request->input('frequency');
        $investmentSetUp->investment_date = $request->input('investment_date');
        $investmentSetUp->investment_amount = $request->input('investment_amount');
        $investmentSetUp->returns = $request->input('returns');
        $investmentSetUp->payment_method = 'debit card';
        $investmentSetUp->balance = '0.00';
        $save = $investmentSetUp->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }
    }
    public function fundInvestment(Request $request){
        $loggedInUser = Auth::user();
        $checkInvestment = Investment::where([
            'user_name' => $loggedInUser->username
        ]);
        $newBalance = $checkInvestment->balance + $request->input('deposit');
        $updateBalance = DB::table('investments')->where('user_name', $loggedInUser->username)->update([
            'balance'=> $newBalance
        ]);
        if ($updateBalance) {
            $fundInvestment = new Transaction;
            $fundInvestment->user_name = $loggedInUser->username;
            $fundInvestment->transaction_type = "fund investment";
            $fundInvestment->amount = $request->input('deposit');
            $fundInvestment->account = 'investment';
            $fundInvestment->status = 'pending';
            $fundInvestment->payment_method = 'debit card';
            $save = $fundInvestment->save();
            if ($save) {
                return response()->json([
                    "status_code"=> 200,
                    "message" => "OK"
                ], 200);
            }
        }else{
            return response()->json([
                "status_code"=> 422,
                "message" => "Could not process deposit"
            ], 422);
        }
    }
    public function sellUnit(Request $request){
        $loggedInUser = Auth::user();
        $sellUnit = new Transaction;
        $sellUnit->user_name = $loggedInUser->username;
        $sellUnit->transaction_type = "sell units";
        $sellUnit->unit = $request->input('units');
        $sellUnit->amount = $request->input('units') * 100;
        $sellUnit->account = 'investment';
        $sellUnit->status = 'pending';
        $sellUnit->payment_method = 'debit card';
        $save = $sellUnit->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }else{
            return response()->json([
                "status_code"=> 422,
                "message" => "Could not process sale"
            ], 422);
        }
    }

}
