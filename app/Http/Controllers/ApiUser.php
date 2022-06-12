<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiUser extends Controller 
{
    //

    public function getUser()
    {
        return $this->responseData(Auth::user(), 200);
    }
    public function getAllUser()
    {
        $users = DB::table('users')->all();
        return $this->responseData($users, 200);
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phone' => 'required|unique:users',
        ]);

        if ($validator->fails()) {
            return $this->responseData($validator->errors(), 400);
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
            return $this->responseData(['error' => 'Unauthorized Access'], 400);
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
            return $this->responseData($validator->errors(), 400);
        } else if ((Hash::check(request('password'), Auth::user()->password))) {
            DB::table('users')->where('id', '=', Auth::user()->id)->update([
                'password' =>  bcrypt($request['new_password'])
            ]);
            return $this->responseMessage('success', 200);
        } else {
            return $this->responseMessage('inccorect pass', 400);
        }
    }

    public function forgotPass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($validator->fails()) {
            return $this->responseMessage($validator->errors(), 400);
        }
        $data = DB::table('users')->where('phone', $request->phone)->first();
        if ($data != null) {
            DB::table('users')->where('phone', $request->phone)->update([
                'password' =>  bcrypt($request['new_password'])
            ]);
            return $this->responseMessage("success", 200);
        } else {
            return $this->responseMessage("can't find phone number", 400);
        }
    }

    public function checkPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);
        $data = DB::table('users')->where('phone', $request->phone)->first();
        if ($validator->fails()) {
            return $this->responseMessage($validator->errors(), 400);
        } else if ($data != null) {
            return $this->responseMessage('have', 200);
        } else {
            return $this->responseMessage("Can't find phone number", 400);
        }
    }
    public function changeImage(Request $request)
    {
        if ($request->has('str')) {
            $server_storage = 'http://139.162.56.4:88/';
            $data = $request->str;  // your base64 encoded

            $pos  = strpos($data, ';');
            $a = substr($data, 0, $pos);
            $type = explode('/', $a)[1];

            $image = str_replace('data:image/' . $type . ';base64,', '', $data);
            $image = str_replace(' ', '+', $image);
            $imageName = substr(md5(mt_rand()), 0, 7) . '.' . $type;

            $path = 'images/' . $imageName;
            //file_put_contents($path, base64_decode($image) );
            Storage::disk('public')->put($path, base64_decode($image));
            $link = $server_storage . $path;
            $user_id = Auth::user()->id;
            DB::table('users')->where('id', $user_id)->update([
                'image' =>  $link
            ]);
            return $this->responseData(['link' => $link], 200);
        } else {
            return $this->responseMessage('update image error', 400);
        }
    }
}
