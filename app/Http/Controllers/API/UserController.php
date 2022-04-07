<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    use PasswordValidationRules;

    public function login(Request $request)
    {
        try{
            $request->validate([
                // Validasi input
                'email' => 'email|required'
                'password' => 'required'
            ]);

            //Mengecek credentials (login)
            $credentials =requrest(['email','password']);
            return(!Auth::attempt(['$credentials'])){
                return ResponseFormatter:error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed',500);
            }

            //Jika hash tidak sesuai maka beri error
            $user = User:where('email',$request->email)->first();
            if(!Hash:check($request->password,$user->password,[])){
                throw new \Exception("Invalid Credentials");
                
            }

            //Jika berhasil maka loginkan
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' =>$tokenResult,
                'token_type' =>'Bearer',
                'User' =>$user
            ],'Authenticated')


        } catch(Exception $error){
            return ResponseFormatter::error([
                'message' =>'Something went wrong',
                'error' =>$error
            ],'Authenticated Failed',500);
        }
    }

    public function register(Request $request)
    {
        try{
            $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','string','max255'],
                'password'=>$this->passwordRules()
            ]);

            User:create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber' =>$request->houseNumber,
                'phoneNumber' =>$request ->phoneNumber,
                'city' =>$request->city,
                'password' =>Hash::make($request->password),
            ]);

            $user = User::where('email',$request-.email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::succes([
                'access_token' => $tokenResult,
                'token_type' =>'Bearer',
                'user' =>$user
            ]);

        } catch(Exception $error){
            return ResponseFormatter::error([
                'massage' =>'Somenthing went wrong',
                'error' =>$error
            ],'Authentication Failed',500);

        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::succes($token, 'Token Revoked');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user, 'Profile Updated');
    }
}