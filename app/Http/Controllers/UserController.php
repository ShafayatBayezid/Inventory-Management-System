<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class UserController extends Controller
{
    public function userRegistration(Request $request) {
        try {
            User::create([
                'firstName' => $request->input('firstName'),
                'lastName' => $request->input('lastName'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => $request->input('password'),
            ]);

            return response()->json([
                'success' => 'Success',
                'message' => 'User Registration Successful'
            ], 200);
        }
        catch (Exception $e){
            return response()->json([
                'success' => 'Failed',
                'message' => 'User Registration Failed'
            ], 404);
        }
    }

    public function userLogin(Request $request){
        $isUser = User::where('email', '=', $request->input('email'))
            ->where('password', '=', $request->input('password'))
            ->count();

        if($isUser == 1){
            //User login - JWT token issue
            $token = JWTToken::createToken($request->input('email'));
            return response()->json([
                'status' => 'Success',
                'message' => 'User Login Successful',
                'token' => $token
            ], 200);
        }else{
            return response()->json([
                'status' => 'Failed',
                'message' => 'Unauthorized User'
            ], 404);
        }
    }
}
