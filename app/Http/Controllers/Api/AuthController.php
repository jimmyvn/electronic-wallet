<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller {
    public $successStatus = 200;
    public function register(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'username'  => 'required|unique:users,username|min:4|max:100',
                'email'     => 'required|email|unique:users,email',
                'name'      => 'required',
                'password'  => 'required|min:4|max:100|confirmed',
                'password_confirmation' => 'required',
            ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success = [
            'user' => $user,
            'token' => $user->createToken('AppName')->accessToken
        ];
        return response()->json([
            'data'   => $success,
        ], $this->successStatus);
    }


    public function login(Request $request){
        if(Auth::attempt(
            [
                'username' => $request->username,
                'password' => $request->password
            ])) {
            $user = Auth::user();
            $success = [
                'user' => $user,
                'token' => $user->createToken('AppName')->accessToken
            ];
            return response()->json([
                'data' => $success,
            ], $this->successStatus);
        } else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function getUser() {
        $user = Auth::user();
        return response()->json([
            'data' => $user
        ], $this->successStatus);
    }
}
