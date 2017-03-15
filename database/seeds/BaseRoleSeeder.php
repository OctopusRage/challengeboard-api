<?php

use Illuminate\Database\Seeder;

class BaseRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'role' => 'admin',
        ]);
        DB::table('roles')->insert([
            'role' => 'teacher',
        ]);
        DB::table('roles')->insert([
            'role' => 'student',
        ]);
    }
}
