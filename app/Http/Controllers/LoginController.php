<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Usuario;

class LoginController extends Controller
{
    public function index() {
        if(auth()->check()){
            return redirect('/home');
        }else{
            return view('login');
        }
    }
    
    
    public function login(Request $request) {
        $request->validate([
           'username' => 'required|email', 
           'pass'    => 'required'
        ]);
        
        
        $user = Usuario::where('Correo', $request->username)->where('Password', md5($request->pass))->first();
               
        
        if($user){
//            dd($user->id);
            Auth::loginUsingId($user->id);
//            dd(auth());
            return redirect('/home');
        }
        
        return redirect('/');
        
    }
    
    public function logout() {
        auth()->logout();
        return redirect('/');
    }
}
