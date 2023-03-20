<?php

namespace Database\Seeders;

use App\Modules\Admin\User\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders;

class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('users')->insert([
                'firstname'=>'admin',
                'lastname'=>'admin',
                'telephone'=>'111111111111',
                'email'=>'admin@admin.com',
                'password'=>bcrypt('admin'),
                'status'=>'1',
            ]);

    }
}
