<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();
        $json = File::get("database/data-sample/users.json");
        $data = json_decode($json);
        foreach ($data as $obj) {        
            User::create(array('name' => $obj->name, 
                                'email' => $obj->email, 'password' => Hash::make($obj->password)));}
    }
}
