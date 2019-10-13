<?php

use App\Models\Role;
use Laravel\Passport\Passport;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $clients = Passport::client()->get();

        Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'title' => 'Super Admin',
                'choosable' => 'Y',
                'client_ids' => $clients->where(
                    'name', 'Web.Admin'
                )->pluck('id')->toArray(),
            ]
        );
        Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'title' => 'Admin',
                'choosable' => 'Y',
                'client_ids' => $clients->where(
                    'name', 'Web.Admin'
                )->pluck('id')->toArray(),
            ]
        );

        Role::firstOrCreate(
            ['name' => 'visitor'],
            [
                'title' => 'Visitor',
                'choosable' => 'Y',
                'client_ids' => $clients->whereIn(
                    'name', ['Web.Front-End', 'iOS', 'Android']
                )->pluck('id'),
            ]
        );
    }
}
