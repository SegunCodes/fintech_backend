<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Group_member;
use App\Models\Invite;
use App\Models\Saving;
use App\Models\Thrift_member;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GroupedController extends Controller
{
    public function groupSaving(Request $request){
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
            'invite_role' => 'required',
            'fund_collector' => 'required',
            'member_limit' => 'required',
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
            $groupSaving = new Saving;
            $groupSaving->user_name = $loggedInUser->username;
            $groupSaving->savings_type = 'group savings';
            $groupSaving->savings_name = $request->input('group_name');
            $groupSaving->frequency = $request->input('frequency');
            $groupSaving->target_amount = $request->input('target_amount');
            $groupSaving->monthly_deposit = $request->input('monthly_deposit');
            $groupSaving->debit_day = $request->input('debit_day');
            $groupSaving->start_date = $request->input('start_date');
            $groupSaving->stop_date = $request->input('stop_date');
            $groupSaving->withdrawal_type = $request->input('withdrawal_type');
            $groupSaving->withdrawal_account_name = $request->input('withdrawal_account_name');
            $groupSaving->withdrawal_account_number = $request->input('withdrawal_account_number');
            $groupSaving->invite_role = $request->input('invite_role');
            $groupSaving->fund_collector = $request->input('fund_collector');
            $groupSaving->member_limit = $request->input('member_limit');
            $groupSaving->card_no = $request->input('card_no');
            $groupSaving->card_expiry_date = $request->input('card_expiry_date');
            $groupSaving->card_cvv = $request->input('card_cvv');
            $groupSaving->payment_method = 'debit card';
            $groupSaving->balance = '0.00';
            $save = $groupSaving->save();
            if ($save) {
                $members = new Group_member;
                $members->group_id = $groupSaving->id;
                $members->users = $loggedInUser->username;
                $members->role = "admin";
                $save2 = $members->save();
                if ($save2) {
                    return response()->json([
                        "status_code"=> 200,
                        "message" => "OK"
                    ], 200);
                }
            }
        }       
    }
    public function inviteMember(Request $request){
        $rules = array(
            'username' => 'required|exists:users,username',
            'group_id' => 'required|exists:savings,id'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $checkGroup = Saving::where([
            'id' => $request->input('group_id'),
            'savings_type' => 'group savings',
            'user_name' => $loggedInUser->username
        ])->first();
        if ($checkGroup) {
            $checkInvite = Invite::where([
                'sender' => $loggedInUser->username,
                'receiver' => $request->input('username'),
                'group_id' => $request->input('group_id')
            ])->first();
            if ($checkInvite) {
                return response()->json([
                    "status_code"=> 201,
                    "message" => "You have sent this user an invite already"
                ], 201);
            }else{
                $invitation = new Invite;
                $invitation->sender = $loggedInUser->username;
                $invitation->group_id = $request->input('group_id');
                $invitation->receiver = $request->input('username');
                $invitation->status = 'pending';
                $save = $invitation->save();
                if ($save) {
                    return response()->json([
                        "status_code"=> 200,
                        "message" => "OK"
                    ], 200);
                }
            }
        }else{
            return response()->json([
                "status_code"=> 401,
                "message" => "Something went wrong"
            ], 401);
        }
    }
    public function thrift(Request $request){
        $rules = array(
            'thrift_title' => 'required',
            'frequency' => 'required',
            'member_limit' => 'required',
            'safe_amt' => 'required',
            'rotating_amt' => 'required',
            'debit_day' => 'required',
            'start_date' => 'required',
            'stop_date' => 'required',
            'position' => 'required',
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
            $thrift = new Saving;
            $thrift->user_name = $loggedInUser->username;
            $thrift->savings_type = 'thrift';
            $thrift->savings_name = $request->input('thrift_title');
            $thrift->frequency = $request->input('frequency');
            $thrift->member_limit = $request->input('member_limit');
            $thrift->safe_amount = $request->input('safe_amt');
            $thrift->rotating_amount = $request->input('rotating_amt');
            $thrift->debit_day = $request->input('debit_day');
            $thrift->start_date = $request->input('start_date');
            $thrift->stop_date = $request->input('stop_date');
            $thrift->position = $request->input('position');
            $thrift->withdrawal_account_name = $request->input('withdrawal_account_name');
            $thrift->withdrawal_account_number = $request->input('withdrawal_account_number');
            $thrift->card_no = $request->input('card_no');
            $thrift->card_expiry_date = $request->input('card_expiry_date');
            $thrift->card_cvv = $request->input('card_cvv');
            $thrift->balance = '0.00';
            $thrift->payment_method = 'debit card';
            $save = $thrift->save();
            if ($save) {
                $members = new Thrift_member;
                $members->group_id = $thrift->id;
                $members->users = $loggedInUser->username;
                $members->position = $request->input('position');
                $save2 = $members->save();
                if ($save2) {
                    return response()->json([
                        "status_code"=> 200,
                        "message" => "OK"
                    ], 200);
                }
            }
        }
    }
    public function inviteThrift(Request $request){
        $rules = array(
            'username' => 'required|exists:users,username',
            'thrift_id' => 'required|exists:savings,id'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'status_code' => 422,
                'message' => implode(",",$validator->errors()->all())
            ]));
        }
        $loggedInUser = Auth::user();
        $checkThrift = Saving::where([
            'id' => $request->input('thrift_id'),
            'savings_type' => 'thrift',
            'user_name' => $loggedInUser->username
        ])->first();
        if ($checkThrift) {
            $checkInvite = Invite::where([
                'sender' => $loggedInUser->username,
                'receiver' => $request->input('username'),
                'thrift_id' => $request->input('thrift_id')
            ])->first();
            if ($checkInvite) {
                return response()->json([
                    "status_code"=> 201,
                    "message" => "You have sent this user an invite already"
                ], 201);
            }else{
                $invitation = new Invite;
                $invitation->sender = $loggedInUser->username;
                $invitation->thrift_id = $request->input('thrift_id');
                $invitation->receiver = $request->input('username');
                $invitation->status = 'pending';
                $save = $invitation->save();
                if ($save) {
                    return response()->json([
                        "status_code"=> 200,
                        "message" => "OK"
                    ], 200);
                }
            }
        }else{
            return response()->json([
                "status_code"=> 401,
                "message" => "Something went wrong"
            ], 401);
        }
    }
}
