<?php

namespace App\Http\Controllers;

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
}
