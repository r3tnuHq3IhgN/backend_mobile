<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiUser extends Controller
{
    //

    public function getUser() 
    {
        return $this->responseData(Auth::user(),200);
    }
    public function getAllUser()
    {
        $users = DB::table('users')->all();
        return $this->responseData($users,200);
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseData($validator->errors(), 202);
        }
        $allData = $request->all();
        $allData['password'] = bcrypt($allData['password']);
        $user = User::create($allData);
        $resArr = [];
        $resArr['token'] = $user->createToken('api-token')->accessToken;
        $resArr['name'] = $user->name;
        return $this->responseData($resArr, 200);
    }
    public function login(Request $request)
    {
        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            $user = Auth::user();
            $resArr = [];
            $resArr['token'] = $user->createToken('api-token')->accessToken;
            $resArr['name'] = $user->name;
            return  $this->responseData($resArr, 200);
        } else {
            return $this->responseData(['error' => 'Unauthorized Access'], 203);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'logout'];
        return $this->responseData($response, 200);
    }

    public function checkLoggerIn(Request $request)
    {
        return $this->responseData($request->user('api'));
    }

    public function changePass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|same:password',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return $this->responseData($validator->errors(), 202);
        } else if ((Hash::check(request('password'), Auth::user()->password))) {
            DB::table('users')->where('id', '=', Auth::user()->id)->update([
                'password' =>  bcrypt($request['new_password'])
            ]);
            return $this->responseMessage('success');
        } else {
            return $this->responseMessage('inccorect pass');
        }
    }
}
