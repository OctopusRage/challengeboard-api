<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = DB::table('roles')->where('role','=','admin')->first()->id;
        DB::table('users')->insert([
            'nim' => 'admin',
            'encrypted_password' => Hash::make('secretpassword'),
            'auth_token' => str_random(20),
            'name' => 'admin',
            'role_id' => $admin_role
        ]);
    }
}
