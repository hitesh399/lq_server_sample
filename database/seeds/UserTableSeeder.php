<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = Role::all();
        foreach (range(1, 100) as $no) {
            $user = factory(App\Models\User::class)->create();
            if ($roles->isNotEmpty()) {
                $user->roles()->sync(
                    $roles->random()->first()->pluck('id')->toArray()
                );
            }
        }
    }
}
