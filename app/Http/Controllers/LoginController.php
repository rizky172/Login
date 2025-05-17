<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Libs\Repository\Login;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {
            $success = Login::login($request->username, $request->password);

            if (!$success) {
                return back()->withErrors(['username' => 'Username atau Password salah'])->withInput();
            }

            return redirect()->route('dashboard')->with('success', 'Login Berhasil.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function indexRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        try {
            Login::register($request->name, $request->username, $request->email, $request->password, $request->confirm_password);
    
            return redirect()->route('halRegister')->with('success', 'Registrasi berhasil silahkan Login!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function indexForgotPassword()
    {
        return view('forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        try {
            Login::forgotPassword($request->username, $request->password, $request->confirm_password);
    
            return redirect()->route('index.forgot.password')->with('success', 'Password berhasil diganti.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Logout Berhasil.');
    }

}
