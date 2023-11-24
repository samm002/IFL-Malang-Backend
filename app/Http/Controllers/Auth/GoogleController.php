<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $userFromGoogle = Socialite::driver('google')->user();

        // Ambil user dari database berdasarkan google user id
        $userFromDatabase = User::where('google_id', $userFromGoogle->getId())->first();

        // Jika tidak ada user maka buat user baru
        if (!$userFromDatabase) {
            $newUser = new User([
                'google_id' => $userFromGoogle->getId(),
                'name' => $userFromGoogle->getName(),
                'email' => $userFromGoogle->getEmail(),
                'password' => bcrypt('12345678')
            ]);

            $newUser->save();

            // Login user yang baru dibuat
            auth('web')->login($newUser);
            session()->regenerate();

            return redirect('/welcome');
        }

        // Jika ada user langsung login saja
        auth('web')->login($userFromDatabase);
        session()->regenerate();

        return redirect('/welcome');

    }
}
