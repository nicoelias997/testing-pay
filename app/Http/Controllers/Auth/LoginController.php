<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login page
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(LoginRequest $request)
    {
        $request->validated();
        
        $request->authenticate();

        // $request->cheackState();
        
        $request->session()->regenerate();
        
        return redirect()->intended(RouteServiceProvider::getHomeRoute());
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unauthenticate(Request $request)
    {
        Auth::logout();
     
        $request->session()->invalidate();
     
        $request->session()->regenerateToken();
     
        return redirect('/');
    }
}
