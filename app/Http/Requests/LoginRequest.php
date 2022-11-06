<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password'=>'required'
        ];
    }
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => implode(",",$validator->errors()->all())
        ], 400));
    }
    public function authenticate(){
        $this->ensureIsNotRateLimited();
        if (!Auth::attempt($this->only('email','password'))) {
            RateLimiter::hit($this->throttleKey(), 120);
            throw new HttpResponseException(response()->json([
                'status' => false,
                'message' => 'Incorrect Password'
            ], 400));
        }
        RateLimiter::clear($this->throttleKey(), 120);
    }
    public function ensureIsNotRateLimited(){
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 4)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw new HttpResponseException(response()->json([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60)
            ]),
        ]));
    }
    public function throttleKey(){
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
