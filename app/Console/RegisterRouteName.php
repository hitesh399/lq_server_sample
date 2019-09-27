<?php

namespace App\Console;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\PermissionGroup;

class RegisterRouteName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:route-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Register the route name in db.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routes = \Route::getRoutes();
        $route_names = $routes->getRoutesByName();

        $group = PermissionGroup::firstOrCreate(['name'=> 'new'], ['description' => 'New']);

        foreach ($route_names as $name => $route) {
            $uri_arr = explode('/', $route->uri);

            if (count($uri_arr) === 3) {
                $group = PermissionGroup::firstOrCreate(['name'=> title_case($uri_arr[1])], ['description' => $uri_arr[1] ]);
            }

            $permission = Permission::where('name', $name)->first();

            if (!$permission) {
                Permission::create(['name' => $name, 'permission_group_id' => $group->id]);
                $this->info('Route '. $name .' has been registered.');
            }
        }

        $this->alert('All Route name has been updated.');
    }
}
