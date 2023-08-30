<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PHPUnit\Exception;

class JWTToken
{
     public static function createToken($email)
    {
        $key = env('JWT_KEY');
        $payload = array(
            "iss" => "laravel-token",
            "iat" => time(),
            "exp" => time() + 60 * 60,
            "userEmail" => $email,
        );
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function createTokenForSetPassword($email)
    {
        $key = env('JWT_KEY');
        $payload = array(
            "iss" => "laravel-token",
            "iat" => time(),
            "exp" => time() + 60 * 10,
            "userEmail" => $email,
        );
        return JWT::encode($payload, $key, 'HS256');
    }


    public static function verifyToken($token)
    {
        try {
            $key = env('JWT_KEY');
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            return $decode->userEmail;
        }catch (Exception $e){
            return 'Unauthorized';
        }
    }

}
