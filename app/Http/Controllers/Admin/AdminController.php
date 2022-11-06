<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminx;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\VerifyAdminRequest;
use Mail;

class AdminController extends Controller
{
    public function createAdmin(AdminRegisterRequest $request){
        $admin = new Adminx;
        $admin->email=$request->input('email');
        $admin->role=$request->input('role');
        $admin->password=$request->input('password');
        $admin->token='';
        $save = $admin->save();
        
        if ($save) {
            // return $user;
            return response()->json([
                "status_codes"=> 201,
                "email" => $request->input('email'),
                "role" => $request->input('role'),
                "message"=> "OK"
            ], 201);
        }else{
            return response()->json(422);
        }
    }
    function adminLogin(AdminLoginRequest $request){
        $checkAdmin = Adminx::where([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ])->first();
        if ($checkAdmin) {
            $checkAdmin->token = random_int(10000,99999);
            $body = "An attempted login attempt was made to your <b>Lifesaver</b> administrative account
            associated with ".$request->input('email').", <br> Your reset token is <b>".$checkAdmin->token."</b>";
            
            \Mail::send('admin-otp', ['body'=>$body], function($message) use ($request){
                $message->from('tech@lifesavers.ng', 'Lifesaver');
                $message->to($request->input('email'), 'Lifesaver')
                ->subject('ADMIN OTP');
            });
            $save = $checkAdmin->save();
            if ($save) {
                return response()->json([
                    "status_codes" => 200,
                    "otp" => $checkAdmin->token,
                    "message"=>"Ok"
                ], 200);
            }
        }else{
            return response()->json([
                "status_codes" => 400,
                "message"=> "Incorrect email or password"
            ], 400);
        }
    }
    public function verifyAdminOtp(VerifyAdminRequest $request){
        $updateAdmin = Adminx::where([
            'token'=> $request->input('token')
        ])->first();
        $updateAdmin->token = '';
        $save = $updateAdmin->save();
        if ($save) {
            return response()->json([
                "status_code"=> 200,
                "message" => "OK"
            ], 200);
        }else{
            return response()->json([
                "status_codes" => 500,
                "message"=>"Internal Server Error - Could not update user"
            ], 500);
        }
    } 
    public function addAdmin(Request $request){
        $admin = new Adminx;
        $admin->role=$request->input('role');
        $admin->password=$request->input('limit');
        $admin->token='';
        $save = $admin->save();
        
        if ($save) {
            // return $user;
            return response()->json([
                "status_codes"=> 201,
                "limit" => $request->input('limit'),
                "role" => $request->input('role'),
                "message"=> "OK"
            ], 201);
        }else{
            return response()->json(422);
        }
    }
}
