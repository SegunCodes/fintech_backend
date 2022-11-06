<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
class UserController extends Controller
{
    public function register(Request $request){
        $rules = array(
            'first_name'=>'required',
            'last_name'=>'required',
            'email'=>'required|email|unique:users,email',
            'username'=>'required|unique:users,username',
            'phone'=>'required',
            'password'=>['required', Password::min(6)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ]
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $user = new User;
        $user->first_name=$request->input('first_name');
        $user->last_name=$request->input('last_name');
        $user->email=$request->input('email');
        $user->username=$request->input('username');
        $user->phone=$request->input('phone');
        $user->password=Hash::make($request->input('password'));
        $user->email_verified='0';
        $user->bvn_status='unverified';
        $user->status='unverified';
        $user->join_date=date("M-d-Y");
        $user->token = random_int(10000,99999);
        $save = $user->save();
        
        $message = 'Dear '.$request->input('first_name') .',<br><br>';
        $message .= 'Thanks for joining Fintech, we need you to verify your email address
        to complete setting up your account <br><br>';
        $message .= 'Your OTP is <b>'.$user->token.'</b><br><br>';
        $message .= 'Fintech';
        $mail_data = [
            'recipient'=>$request->input('email'),
            'fromMail'=>'seguncodes07@gmail.com',
            'fromName'=>'FINTECH APP - ACCOUNT VERIFICATION OTP',
            'subject'=>'ACCOUNT VERIFICATION OTP',
            'body'=>$message,
        ]; 
        Mail::send('email-verify', $mail_data, function($message) use($mail_data){
            $message->to($mail_data['recipient'])
                    ->from($mail_data['fromMail'], $mail_data['fromName'])
                    ->subject($mail_data['subject']);
        });
        if ($save) {
            // return $user;
            return response()->json([
                'status' => true,
                "message"=> "OK",
                "token" => $user->token
            ], 201);
        }else{
            return response()->json(500);
        }
    } 


    public function verifyUserOtp(Request $request){
        $rules = array(
            'token' => 'required|exists:users,token'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $updateUser = User::where([
            'token'=> $request->input('token')
        ])->first();
        $updateUser->email_verified = '1';
        $updateUser->status = 'verified';
        $updateUser->token = '';
        $save = $updateUser->save();
        if ($save) {
            return response()->json([
                "status"=> true,
                "message" => "OK"
            ], 200);
        }else{
            return response()->json([
                "status" => false,
                "message"=>"Could not update user"
            ], 400);
        }
    } 

    function login(LoginRequest $request){
        $request->authenticate();
        $user = Auth::user();
        if ($user->status == "disabled") {
            Auth::guard('web')->logout();
            return response()->json([
                "status" => false,
                "message"=>"Your account has been disabled"
            ], 400);
        }else{
            if ($user->email_verified == 0) {
                Auth::guard('web')->logout();
                return response()->json([
                    "status" => false,
                    "message"=>"You need to verify your account. We have sent you an activation link, please check your email"
                ], 400);
            }
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 *24);
            return response([
                "status"=> true,
                "user_details" => Auth::user(),
                "message"=>"OK",
                "auth_token" => $token
            ], 200)->withCookie($cookie);
        }
    }

    function logout(){
        $cookie = Cookie::forget('jwt');
        // Auth::user()->currentAccessToken()->delete();
        Auth::guard('web')->logout(); 
        return response([
            'status' => true,
            "message"=> "User logged out"
        ],200);
    }
    public function User(){
        return Auth::user();
    }
    public function sendResetCode(Request $request){
        $rules = array(
            'email' => 'required|email|exists:users,email'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $UserExist = DB::table('password_resets')->where([
            'email'=>$request->input('email')
        ])->first();
        if ($UserExist) {
            DB::table('password_resets')->where([
                'email'=>$UserExist->email
            ])->delete();
            $token = random_int(10000, 99999);
            DB::table('password_resets')->insert([
                'email'=>$request->input('email'),
                'token'=>$token,
                'created_at'=>Carbon::now()
            ]);
            $body = "We received a request to reset the password for your <b>Lifesaver</b> account
            associated with ".$request->input('email').", <br> Your reset token is <b>".$token."</b>";
            
            Mail::send('email-forgot', ['body'=>$body], function ($message) use ($request) {
                $message->from('seguncodes07@gmail.com', 'Fintech');
                $message->to($request->input('email'), 'Fintech')
                        ->subject('Reset Password Code');
            });
            return response()->json([
                "status"=> true,
                "message"=>"A reset password code has been sent to your mail",
                "code"=>$token,
            ], 200);
        }else{
            $checkUser = DB::table('users')->where([
                'email'=>$request->input('email')
            ])->first();
            if ($checkUser) {
                $token = random_int(10000, 99999);
                DB::table('password_resets')->insert([
                    'email'=>$request->input('email'),
                    'token'=>$token,
                    'created_at'=>Carbon::now()
                ]);
                $body = "We received a request to reset the password for your <b>Lifesaver</b> account
                associated with ".$request->input('email').", <br> Your reset token is <b>".$token."</b>";
                
                Mail::send('email-forgot', ['body'=>$body], function ($message) use ($request) {
                    $message->from('tech@lifesavers.ng', 'Lifesaver');
                    $message->to($request->input('email'), 'Lifesaver')
                            ->subject('Reset Password Token');
                });
                return response()->json([
                    "status"=> true,
                    "message"=>"A reset password code has been sent to your mail",
                    "code"=>$token
                ], 200);
            }
        }
    }
    public function verifyResetCode(Request $request){
        $check_token = DB::table('password_resets')->where([
            'token'=>$request->input('token')
        ])->first();
        if (!$check_token) {
            return response()->json([
                "status"=> false,
                "message"=>"Invalid Token"
            ], 400);
        }else{
            return response()->json([
                "status"=> true,
                "email" => $check_token->email,
                "message"=>"Ok"
            ], 200);
        }
    } 
    public function resetPassword(Request $request){
        $rules = array(
            'email' => 'required|email|exists:users,email',
            'password'=>['required', Password::min(6)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'confirm-password'=>'required|same:password'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => implode(",",$validator->errors()->all())
            ], 400));
        }
        $check_email = DB::table('password_resets')->where([
            'email'=>$request->input('email')
        ])->first();
        if (!$check_email) {
            return response()->json([
                "status_code"=> 422,
                "message"=>"invalid email"
            ], 422);
        }else{
            User::where('email', $request->input('email'))->update([
                'password'=>Hash::make($request->input('password'))
            ]);
            DB::table('password_resets')->where([
                'email'=>$request->input('email')
            ])->delete();
            
            return response()->json([
                "status"=> true,
                "message"=>"Password changed"
            ], 200);
        }        
    }  
}
