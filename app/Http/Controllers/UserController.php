<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller {
    public function index(){ $users = User::orderByDesc('id')->paginate(50); return view('users.index', compact('users')); }
    public function toggle(Request $req, User $user){ if (Gate::denies('manage-users')) abort(403); $user->active = $user->active ? 0 : 1; $user->save(); return redirect()->back()->with('status','User status updated'); }
}
