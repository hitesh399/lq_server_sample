<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
            'title' => 'Super Admin',
            'choosable' => 'Y',
            'landing_page'=>'admin/dashboard'
        ]
        );

        Role::firstOrCreate(
            ['name' => 'visitor'],
            [
            'title' => 'Visitor',
            'choosable' => 'Y',
            'landing_page'=>'/'
        ]
        );
    }
}
