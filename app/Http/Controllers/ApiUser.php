<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiUser extends Controller
{
    //
    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 202);
        }
        $allData = $request->all();
        $allData['password'] = bcrypt($allData['password']);
        $user = User::create($allData);
        $resArr = [];
        $resArr['token'] = $user->createToken('api-token')->accessToken;
        $resArr['name'] = $user->name;
        return response()->json($resArr, 200);
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $resArr = [];
            $resArr['token'] = $user->createToken('api-token')->accessToken;
            $resArr['name'] = $user->name;
            return response()->json($resArr, 200);
        } else {
            return response()->json(['error' => 'Unauthorized Access'], 203);
        }
    }

    public function logout(Request $request){
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'logout'];
        return response($response, 200);
    }

    public function checkLoggerIn(Request $request)
    {
        return response()->json($request->user('api'));
    }
}