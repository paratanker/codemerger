<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function showRegister(){ return view('auth.register'); }
    public function register(Request $req){
        $req->validate(['name'=>'required','email'=>'required|email|unique:users,email','password'=>'required|confirmed']);
        $user = User::create(['name'=>$req->name,'email'=>$req->email,'password'=>Hash::make($req->password),'role'=>'user','active'=>1]);
        Auth::login($user);
        return redirect('/dashboard');
    }
    public function showLogin(){ return view('auth.login'); }
    public function login(Request $req){
        $creds = $req->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($creds)){
            $req->session()->regenerate();
            if (!Auth::user()->active) { Auth::logout(); return back()->withErrors(['email'=>'Account deactivated']); }
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors(['email'=>'Invalid credentials']);
    }
    public function logout(Request $req){ Auth::logout(); $req->session()->invalidate(); $req->session()->regenerateToken(); return redirect('/login'); }
}
