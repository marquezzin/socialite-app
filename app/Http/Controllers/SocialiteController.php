<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    //Redirect para o Login com o Google
    public function googleLogin() {
        return Socialite::driver('google')->redirect();
    }

    //Autenticação do usuário pelo Google
    public function googleAuthentication() {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id',$googleUser->id)->first();

            if ($user) {
                Auth::login($user); //Loga se existe
                return redirect()->route('');
            }else{
                $userData = User::create([
                    'name' => $googleUser->name,    
                    'email' => $googleUser->email,    
                    'password' => Hash::make('Password@1234'),  
                    'google_id' => $googleUser->id,  
                ]);

                if ($userData) {
                    Auth::login($userData); //Loga se existe
                    return redirect()->route('dashboard');
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao autenticar com o Google: ' . $e->getMessage());
            return redirect('/login')->withErrors(['message' => 'Erro ao autenticar com o Google.']);
        }
    }
}
