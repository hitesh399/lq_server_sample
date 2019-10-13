<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\ModelFilters\RoleFilter;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::filter($request->all(), RoleFilter::class)->with('clients')
                    ->lqPaginate();

        return $this->setData($roles)
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);
        $role_data = $request->only(
            [
                'choosable',
                'name',
                'parent_role_id',
                'title',
                'description',
                'settings',
            ]
        );
        $role_data['client_ids'] = $request->clients;
        $role = Role::create($role_data);

        return $this->setData(['role' => $role])->setMessage('Role has been created successfully.')->response();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with(
            'permissions',
            'permissionFields',
            'clients',
            'menuItems'
        )->findOrFail($id);

        return $this->setData(
            [
                'role' => $role,
            ]
        )->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validation($request, $id);

        $role = Role::findOrFail($id);

        $role_data = $request->only(
            [
                'choosable',
                'parent_role_id',
                'title',
                'description',
                'settings',
            ]
        );
        $role_data['client_ids'] = $request->clients;

        $role->update($role_data);
        /**
         * Sync Permission with Role.
         */
        $menu_items = $request->get('menu_items', []);

        $permissions = \App\Models\Permission::join(
            'application_menu_items',
            function ($q) {
                $q->whereRaw('JSON_CONTAINS(application_menu_items.permission_ids, CAST(permissions.id AS CHAR))');
            }
        )->select('permissions.id')->groupBy('permissions.id')
            ->whereIn('application_menu_items.id', $menu_items)
            ->get()->pluck('id')->toArray();

        $role_permissions = [];

        foreach ($permissions as $permission_id) {
            $role_permissions[$permission_id] = ['limitations' => null];
        }

        $_role_permissions = $request->rolePermissions ? $request->rolePermissions : [];
        foreach ($_role_permissions as $role_permission) {
            if (in_array($role_permission['permission_id'], $permissions)) {
                $role_permissions[$role_permission['permission_id']] = [
                    'limitations' => $role_permission['limitations'],
                ];
            }
        }

        // Sync Application Menu
        $role->menuItems()->sync($menu_items);
        // Sync Permission field with Role.
        $role->permissions()->sync($role_permissions);

        $new_role_permissions = $role->permissions()->get()->map(
            function ($q) {
                return [
                    'permission_id' => $q->id,
                    'role_permission_id' => $q->pivot->id,
                ];
            }
        )->keyBy('permission_id')->toArray();

        $permission_fields = [];

        $fields = $request->fields ? $request->fields : [];

        foreach ($fields as $field) {
            $permission_id = $field['permission_id'];
            if (!isset($new_role_permissions[$permission_id]['role_permission_id'])) {
                continue;
            }
            $permission_fields[$field['permission_field_id']] = [
               'authority' => $field['authority'],
               'permission_field_id' => $field['permission_field_id'],
               'permission_id' => $permission_id,
               'role_permission_id' => $new_role_permissions[$permission_id]['role_permission_id'],
            ];
        }
        $role->permissionFields()->sync($permission_fields);

        // Deleting Role detail from cache repo.
        \Cache::forget('permission_role_repository_'.$id);

        return $this->setMessage('Role has been updated successfully.')->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Validate  the given request.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function validation(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
    }
}
