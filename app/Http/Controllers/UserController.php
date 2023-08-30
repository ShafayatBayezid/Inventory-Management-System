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

            $isUserExist = User::where('email', '=', $request->input('email'))
                ->count();

            if($isUserExist == 1){
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
        $email = $request->input('email');
        $password = $request->input('password');
        $isUserExist = User::where('email', '=', $email)
            ->where('password', '=', $password)->count();

        if($isUserExist == 1){
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
        $otp = rand(100000, 999999);

        $isEmailValid = User::where('email', '=', $email)->count();
        if ($isEmailValid == 1){

            // Get user's name from the database
            $user = User::where('email', '=', $email)->get('firstName');

            // Send OTP code to the user email
            Mail::to($email)->send(new OPTMail($otp, $user[0]->firstName));

            // Insert OTP code to the database
            User::where('email', '=', $email)->update(['otp' => $otp]);

            return response()->json([
                'status' => 'Success',
                'message' => '6 digit OTP Code Sent to Your Email'
            ], 200);


        }else{
            return response()->json([
                'status' => 'Failed',
                'message' => 'User Not Found'
            ], 200);
        }
    }

    function verifyOTPCode(Request $request){
        $email = $request->input('email');
        $otp = $request->input('otp');

        $isOTPValid = User::where('email', '=', $email)
            ->where('otp', '=', $otp)->count();

        if ($isOTPValid == 1){
            // Update OTP code to the database
            User::where('email', '=', $email)->update(['otp' => '0']);

            // Reset Password - JWT token issue
            $token = JWTToken::createTokenForSetPassword($email);
            return response()->json([
                'status' => 'Success',
                'message' => 'OTP Code Verified',
                'token' => $token
            ], 200);

        }else{
            return response()->json([
                'status' => 'Failed',
                'message' => 'OTP Code Verification Failed'
            ], 200);
        }
    }

    function resetPassword(Request $request){
        try {
            $email = $request->header('email');
            $password = $request->input('password');

            User::where('email', '=', $email)
                ->update(['password' => $password]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Password Reset Successful'
            ], 200);
        }catch (Exception $e){
            return response()->json([
                'status' => 'Failed',
                'message' => 'Password Reset Failed'
            ], 404);
        }
    }
}
