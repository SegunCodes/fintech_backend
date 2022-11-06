<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Saving;
use App\Models\Investment;

class TransactionController extends Controller
{
    public function getTransactions(){
        $transactions = Transaction::all();
        if($transactions){
            return response()->json($transactions);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //fixed savings transaction
    public function fixedTransactions(){
        $checkTransaction = Transaction::join('savings','savings.savings_name','=','transactions.account')
        ->where('savings.savings_type', 'fixed saving')
        ->get(['transactions.*', 'savings.savings_name']);
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
        
    }
    //personal savings transaction
    public function personalTransactions(){
        $checkTransaction = Transaction::join('savings','savings.savings_name','=','transactions.account')
        ->where('savings.savings_type', 'personal savings')
        ->get(['transactions.*', 'savings.savings_name']);
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //group savings transaction
    public function groupTransactions(){
        $checkTransaction = Transaction::join('savings','savings.savings_name','=','transactions.account')
        ->where('savings.savings_type', 'group savings')
        ->get(['transactions.*', 'savings.savings_name']);
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //thrift savings transaction
    public function thriftTransactions(){
        $checkTransaction = Transaction::join('savings','savings.savings_name','=','transactions.account')
        ->where('savings.savings_type', 'thrift')
        ->get(['transactions.*', 'savings.savings_name']);
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //investment transaction
    public function investmentTransactions(){
        $checkTransaction = Transaction::where([
            'account' => 'investment'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }     
    }
    //airtime transaction
    public function airtimeTransaction(Request $request){
        $checkTransaction = Transaction::where([
            'transaction_type' => 'airtime'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //cable transaction
    public function cableTransaction(Request $request){
        $checkTransaction = Transaction::where([
            'transaction_type' => 'cable'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //electricity transaction
    public function electricityTransaction(Request $request){
        $checkTransaction = Transaction::where([
            'transaction_type' => 'electricity'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    } 
    //internet transaction
    public function internetTransaction(Request $request){
        $checkTransaction = Transaction::where([
            'transaction_type' => 'internet'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
    //data transaction
    public function dataTransaction(Request $request){
        $checkTransaction = Transaction::where([
            'transaction_type' => 'data'
        ])->get();
        if ($checkTransaction) {
            return response()->json($checkTransaction);
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "No transaction found"
            ], 400);
        }
    }
}
