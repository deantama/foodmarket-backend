<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facedes\Auth;
use Illuminate\Support\Facedes\Hash;

class UserController extends Controller
{

    use PasswordValidationRules
    public function login (Request :$request)
    {
        try{

            //validasi input
            $request -> validate({
                'email' => 'email|required'
                'password' => 'required'
            });

            // Mengecek credentials (login)
            $credentials = request (['email', 'password']);
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'massage' => 'Unauthorized'
                ],'Authentication Failed',500);
            }

            // Jika hash tidak sesuai maka beri error
            $user =User:where ('email'. $request->email)->first();
            if(!Hash::check($request->password,$user ->password,[])){
                throw new \Exception('Invalid Credentials');
            }

            // Jika berhasil maka loginkan
            $tokenResult =$user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success({
                'acces_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            },'Authenticated');


        } catch(Exception $error){
            return ResponseFormatter::error([
                'massage' => 'Somenthing went wrong',
                'error' =>$error
            ], 'Authentication Failed',500);
        }
    }

    public function register (Request $request)
    {
        try{
            $request ->validate([
                'name' => ['required','string','max:255'],
                'name' => ['required','string','emai','max:255','unique:users'],
                'password' => $this ->passwordRules()
            ]);

            User:create([
                'name' => $request ->name,
                'email' => $request ->email,
                'address' => $request ->address,
                'houseNumber' =>$request ->houseNumber,
                'phoneNumber' => $request ->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User:where('email',$request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' =>$user
            ]);

        } catch (Exception $error){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ],'Authentication Failed',500);
        }
    }
}
