<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class DatabaseSeeder extends Seeder {
    public function run(){ User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>Hash::make('password'),'role'=>'super_admin','active'=>1]); User::create(['name'=>'Demo User','email'=>'user@example.com','password'=>Hash::make('password'),'role'=>'user','active'=>1]); }
}
