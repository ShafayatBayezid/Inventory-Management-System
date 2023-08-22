<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OPTMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class UserController extends Controller
{
    function userRegistration(Request $request) {
        try {

            $isUser = User::where('email', '=', $request->input('email'))
                ->count();

            if($isUser == 1){
                return response()->json([
                    'success' => 'Failed',
                    'message' => 'User Already Exists'
                ], 200);

            }else{
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

        }
        catch (Exception $e){
            return response()->json([
                'status' => 'Failed',
                'message' => 'User Registration Failed'
            ], 404);
        }
    }

    function userLogin(Request $request){
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
            ], 200);
        }
    }

    function sendOTPCode(Request $request){
        $email = $request->input('email');
        $otp = rand(1000, 9999);

        $isUser = User::where('email', '=', $email)
            ->count();
        if ($isUser == 1){

            // Get user name from the database
            $userName = User::where('email', '=', $email)
                ->get('firstName');

            // Send OTP code to the user email
            Mail::to($email)->send(new OPTMail($otp, $userName[0]->firstName));

            // Insert OTP code to the database
            User::where('email', '=', $email)
                ->update([
                    'otp' => $otp
                ]);

            return response()->json([
                'status' => 'Success',
                'message' => '4 digit OTP Code Sent to Your Email'
            ], 200);


        }else{
            return response()->json([
                'status' => 'Failed',
                'message' => 'User Not Found'
            ], 200);
        }
    }
}
