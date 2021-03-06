<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function show() {
        $users = User::all();

        return view('dashboard.users', compact('users'));
    }
    public function register(){
        $data['title'] = "Register";
        return view('user/register', $data);
    }

    public function register_action(Request $request){
        $request->validate([
            'name'=>'required',
            'nipnim'=>'required|unique:users',
            'email'=>'required|unique:users',
            'notelp'=>'required',
            'kk'=>'required',
            'password'=>'required',
            'password_confirmation'=>'required|same:password',
            'is_admin'=>'required',
        ]);
        $user = new User([
            'name' => $request->name,
            'nipnim' => $request->nipnim,
            'email' => $request->email,
            'notelp'=>$request->notelp,
            'kk'=>$request->kk,
            'password' => Hash::make($request->password),
            'is_admin'=>$request->is_admin,
        ]);
        $user->save();
        return redirect()->route('register_success')->with('success', 'Registrasi Berhasil. Silahkan masuk');
    }

    public function login(){
        $data['title'] = 'Login';
        return view('user/master', $data);
    }
    public function login_action(Request $request){
        $request->validate([
            'nipnim'=>'required',
            'password'=>'required',
        ]);
        if(Auth::attempt(['nipnim'=>$request->nipnim, 'password'=>$request->password])) {
            if(Auth::user()->is_admin == 1){
                $request->session()->regenerate();
                return redirect()->intended('dashboard');
            }
            else{
                return redirect()->route('home');
            }
        }
        return back()->withErrors(['password'=>'Password atau NIM salah!']);
    }
    
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();
        return redirect('/');
    }
}
