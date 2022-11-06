<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function getUsers(){
        $users = User::all();
        if ($users) {
            return response()->json($users);
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No users found"
            ], 201);
        }       
    }
    public function viewUser($id){
        $viewUser =  User::findOrFail($id);
        if ($viewUser) {          
            return response()->json($viewUser) ;
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No user found"
            ], 201);
        }
    }
    public function disableUser($id){
        $disableUser = User::find($id);
        if ($disableUser) {
            $disableUser->status = "disabled";
            $disableUser->save();
            return response()->json([
                "status_codes" => 200,
                "message"=> "disabled"
            ], 200);
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No user found"
            ], 201);
        }
    }
    public function activateUser($id){
        $activateUser = User::find($id);
        if ($activateUser) {
            $activateUser->status = "verified";
            $activateUser->save();
            return response()->json([
                "status_codes" => 200,
                "message"=> "enabled"
            ], 200);
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No user found"
            ], 201);
        }
    }
    public function deleteUser($id){
        $deleteUser = User::find($id);
        if ($deleteUser) {
            $deleteUser->delete();
            return response()->json([
                "status_codes" => 200,
                "message"=> "OK, user deleted"
            ], 200);
        }else{
            return response()->json([
                "status_codes" => 201,
                "message"=> "No user found"
            ], 201);
        }
    }
    // public function searchUser(Request $request){
    //     if (isset($_GET["query"])) {
    //         $search_text = $_GET["query"];
    //         $users = DB::table('users')->
    //         where('username', 'LIKE','%'.$search_text.'%')->get();
    //         return response()->json($users);
    //     }else{
    //         return response()->json([
    //             "status_codes" => 201,
    //             "message"=> "No user found"
    //         ], 201);
    //     }
    // }
}
