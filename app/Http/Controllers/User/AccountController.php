<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Debit_card;
use App\Models\Next_of_kin;
use App\Models\Two_factor_Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
// use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Bvn;

class AccountController extends Controller
{
    public function personalInfo(Request $request){  
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|exists:users,email',
            'username' => 'required|exists:users,username',
            'country' => 'required',
            'phone' => 'required',
            'password'=>'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $loggedInUser = Auth::user();
        if (!Hash::check($request->input('password'), $loggedInUser->password)) {
            return response()->json([
                "status" => false,
                "message"=> "incorrect password"
            ], 400);
        }else{
            // $loggedInUser = Auth::user();
            $loggedInUser->first_name=$request->input('first_name');
            $loggedInUser->last_name=$request->input('last_name');
            $loggedInUser->email=$request->input('email');
            $loggedInUser->username=$request->input('username');
            $loggedInUser->country=$request->input('country');
            $loggedInUser->phone=$request->input('phone');
            $save = $loggedInUser->save();
            if ($save) {
                return response()->json([
                    "status"=> true,
                    "message" => "ok"
                ], 200);
            }
        }
    }

    public function updatePassword(Request $request){
        $rules = array(
            'password'=>'required',
            'new_password'=>['required', Password::min(6)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'confirm_password'=>'required|min:6|same:new_password'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $user = Auth::user();
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                "status" => false,
                "message"=> "incorrect password"
            ], 400);
        }
        $user->password = Hash::make($request->input('new_password'));
        $save = $user->save();
        if ($save) {
            return response()->json([
                "status"=> true,
                "message" => "Ok"
            ], 200);
        }else{
            return response()->json(500);
        }
    }

    public function twoFactorAuth(Request $request){
        $rules = array(
            'question' => 'required',
            'answer' => 'required',
            'password' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $loggedInUser = Auth::user();
        if (!Hash::check($request->input('password'), $loggedInUser->password)) {
            return response()->json([
                "status" => false,
                "message"=> "incorrect password"
            ], 400);
        }else{
            $checkIdExist = Two_factor_Auth::where([
                'user_id'=> $loggedInUser->id
            ])->first();
            if ($checkIdExist) {
                return response()->json([
                    "status"=> false,
                    "message" => "You already have 2FA setup"
                ], 400);
            }else{
                $tfa = new Two_factor_Auth;
                $tfa->user_id=$loggedInUser->id;
                $tfa->question=$request->input('question');
                $tfa->answer=$request->input('answer');
                $save = $tfa->save();
                if ($save) {
                    return response()->json([
                        "status_code"=> 200,
                        "message" => "OK"
                    ], 200);
                }
            }
        }
    }

    public function nextOfKin(Request $request){
        $rules = array(
            'name' => 'required',
            'relationship' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $loggedInUser = Auth::user();
        if (!Hash::check($request->input('password'), $loggedInUser->password)) {
            return response()->json([
                "status" => false,
                "message"=> "incorrect password"
            ], 400);
        }else{
            $checkIdExist = Next_of_kin::where([
                'user_id'=> $loggedInUser->id
            ])->first();
            if ($checkIdExist) {
                return response()->json([
                    "status"=> false,
                    "message" => "You already have setup a next of kin detail"
                ], 400);
            }else{
                $nextKin = new Next_of_kin;
                $nextKin->user_id=$loggedInUser->id;
                $nextKin->name=$request->input('name');
                $nextKin->relationship=$request->input('relationship');
                $nextKin->email=$request->input('email');
                $nextKin->phone=$request->input('phone');
                $save = $nextKin->save();
                if ($save) {
                    return response()->json([
                        "status"=> true,
                        "message" => "okay"
                    ], 200);
                }
            }
        }
    }

    //corrections needed
    public function personalIdentity(Request $request){
        $loggedInUser = Auth::user();
        if (!Hash::check($request->input('password'), $loggedInUser->password)) {
            return response()->json([
                "status" => false,
                "message"=> "incorrect password"
            ], 400);
        }else{
            $url = "https://api.paystack.co/customer/{customer_code}/identification";
            $fields = [
                "country"=> "NG",
                "type" => "bank_account",
                "account_number" => "0123456789",
                "bvn" => "200123456677",
                "bank_code" => "007",
                "first_name" => "Asta",
                "last_name" => "Lavista"
            ];
            $fields_string = http_build_query($fields);
            //open connection
            $ch = curl_init();
            
            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer SECRET_KEY",
                "Cache-Control: no-cache",
            ));
            
            //So that curl_exec returns the contents of the cURL; rather than echoing it
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
            
            //execute post
            $result = curl_exec($ch);
            echo $result;

            if (!$result) {
                return response()->json([
                    "status_code" => 400,
                    "message"=> "unexpected error"
                ], 400);
            }else{
                $loggedInUser->bvn_status = "verified";
                $save = $loggedInUser->save();
                if ($save) {
                    return response()->json($result);
                }
            }
        }
    }

    //corrections needed
    public function bankDetails(Request $request){
        $rules = array(
            'account_no' => 'required',
            'bank_code' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $loggedInUser = Auth::user(); 
        $account = $request->input('account_no');
        $code = $request->input('bank_code');
        $curl = curl_init();  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=$account&bank_code=$code",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_4acba69cf6ba1404f11b6182075596c9be9e68f5",
            "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
            $err2 = json_decode($err);
            return response()->json($err2);
        } else {
            $res = json_decode($response);
            if ($res->status == true) {
                $bank = new Bank;
                $bank->user_id=$loggedInUser->id;
                $bank->account_no=$request->input('account_no');
                $bank->bank=$request->input('bank_code');
                $save = $bank->save();
                if ($save) {
                    return response()->json($res);
                }
            }elseif($res->status == false){
                return response()->json($res);
            }
        }
        
    }

    //delete bank function //corrections needed
    public function deleteBank(Request $id){
        $loggedInUser = Auth::user(); 
        $bank = Bank::find($id);
        $checkUserBank = Bank::where([
            'id' => $id,
            'user_id'=> $loggedInUser->id
        ])->first();
        if ($checkUserBank) {
            $bank->delete();
            return response()->json([
                "status_code"=> 200,
                "message" => "OK, bank deleted"
            ], 200);
        }else{
            return response()->json([
                "status_code"=> 400,
                "message" => "Bad request"
            ], 400);
        }
        
    }

    //corrections needed
    public function debitCard(Request $request){
        $rules = array(
            'card_no' => 'required',
            'expiry_date' => 'required',
            'cvv' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $result = $request->input('card_no');
        $bin = substr($result, 0, 6);
        $curl = curl_init();
  
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/decision/bin/$bin",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_4acba69cf6ba1404f11b6182075596c9be9e68f5",
            "Cache-Control: no-cache",
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $err2 = json_decode($err);
            return response()->json($err2);
        }
        else{
            $res = json_decode($response);
            if ($res->status == true) {
                $debitCard = new Debit_card;
                $debitCard->user_id=$loggedInUser->id;
                $debitCard->card_no=$request->input('card_no');
                $debitCard->expiry_date=$request->input('expiry_date');
                $debitCard->cvv=$request->input('cvv');
                $save = $debitCard->save();
                if ($save) {
                    return response()->json($res);
                }
            }elseif($res->status == false){
                return response()->json($res);
            }
        }
        
    }

    //delete card function //corrections needed
    public function deleteDebitCard(Request $id){
        $loggedInUser = Auth::user(); 
        $card = Debit_card::find($id);
        $checkUserCard = Debit_card::where([
            'id' => $id,
            'user_id'=> $loggedInUser->id
        ])->first();
        if ($checkUserCard) {
            $card->delete();
            return response()->json([
                "status_code"=> 200,
                "message" => "OK, card deleted"
            ], 200);
        }else{
            return response()->json([
                "status_code"=> 400,
                "message" => "Bad request"
            ], 400);
        }
    }

    //corrections needed 
    public function getCard(){
        $loggedInUser = Auth::user();
        // $card = Debit_card::all();
        $checkUserCard = Debit_card::where([
            'user_id'=> $loggedInUser->id
        ])->get();
        if ($checkUserCard) {
            return response()->json($checkUserCard);
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No card found"
            ], 201);
        }       
    }
    // public function viewCard($id){
    //     $loggedInUser = Auth::user();
    //     $viewCard = Bank::where([
    //         'user_id' => $loggedInUser->id
    //     ])->first();
    //     // $viewCard =  Debit_card::findOrFail($id);
    //     if ($viewCard) {          
    //         return response()->json($viewCard) ;
    //     }else{
    //         return response()->json([
    //             "status_codes" => 201,
    //             "message"=> "No card found"
    //         ], 201);
    //     }  
    // }
}
