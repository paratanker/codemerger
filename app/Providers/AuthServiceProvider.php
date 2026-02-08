<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
class AuthServiceProvider extends ServiceProvider { public function register(){} public function boot(){ Gate::define('manage-users', function(User $user){ return $user->role === 'super_admin'; }); } }
