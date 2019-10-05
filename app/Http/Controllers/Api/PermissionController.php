<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Validation\Rule;
use App\ModelFilters\PermissionFilter;
use App\Models\PermissionField;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermissionDataExport;
use App\Imports\PermissionDataImport;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if($request->export == 'excel') {
        //     return Excel::download(new PermissionDataExport, 'permission.xlsx');
        // }

        $permissions = Permission::filter(
            $request->all(), PermissionFilter::class
        )->with('permissionGroup', 'permissionFields')->paginate($request->page_size);

        return $this->setData($permissions)
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);
        $data = $request->only(
            'name',
            'title',
            'is_public',
            'permission_group_id',
            'description',
            'limitations'
        );
        $data['client_ids'] = $request->clients;
        $data['specific_role_ids'] = $request->specific_roles;
        $permission = Permission::create($data);
        $this->_addFormField($permission->id, $request);

        return $this->setData(
            [
                'permission' => $permission
            ]
        )->response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->setData([
            'permission' =>  $this->getPermision($id)
        ])->response();
    }
    /**
     * To get The permission
     */
    private function getPermision($id) {

        return Permission::with(
            [
                'permissionGroup' => function ($q){
                    $q->select('id','name');
                },
                'permissionFields'=> function ($q) {
                    $q->select('id', 'permission_id', 'title', 'client_field', 'table_columns');
                },
                'clients' => function ($q) {
                    $q->select('id', 'name');
                },
                'specificRoles' => function ($q) {
                    $q->select('id', 'name');
                }
            ]
        )->findOrFail($id);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validation($request, $id);

        $permission = Permission::findOrFail($id);
        $data = $request->only(
            'title',
            'is_public',
            'description',
            'limitations',
            'encrypted'
        );
        $permission_group = PermissionGroup::getIdOrMakeNew(
            $request->permission_group
        );
        $data['permission_group_id'] = $permission_group;
        $data['client_ids'] = $request->clients;
        $data['specific_role_ids'] = $request->specific_roles;
        $permission->update($data);
        $client_fields = $this->_addFormField($id, $request);

        /**
         * To renove the fields from database if user deleted client side.
         */
        $delete_pf = PermissionField::where('permission_id', $id);
        if (count($client_fields)) {
            $delete_pf->whereNotIn('client_field', $client_fields);
        }
        $delete_pf->delete();

        # Deleting Permissions detail from cache repo.
        \Cache::forget('permission_repository');

        return $this->setMessage('Permission has been updated.')->response();
    }
    private function _addFormField($id, Request $request)
    {
        $client_fields = [];
        if ($request->permission_fields && is_array($request->permission_fields)) {
            foreach ($request->permission_fields as $permission_field) {

                $client_fields[] = $permission_field['client_field'];
                PermissionField::updateOrCreate(
                    [
                        'permission_id'=> $id,
                        'client_field'=> $permission_field['client_field']
                    ],
                    $permission_field
                );
            }
        }
        return $client_fields;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return $this->setData(['permission' => $permission])
            ->response();
    }
    /**
     * Upload Permission Data
     */
    public function upload(Request $request) {

        $this->validate($request, [
            'file' => 'required|file'
        ]);

        Excel::import(new PermissionDataImport, $request->file);

        return $this->setMessage('Permissions have imported successfully.')
            ->response();
    }

    /**
     * Validate permission data./
     */
    private function validation($request, $id = null) {
        $rules =  [
            'name' => ['required', Rule::unique('permissions')->ignore($id)],
            'title' => ['required', 'max:255'],
            'is_public' => ['required', 'in:Y,N'],
            'permission_group_id' => 'integer',
            'description' => 'string|max:255',
            'limitations' => 'nullable|Array',
            'encrypted' => 'nullable|Array',
            // 'permission_fields.*.title' => 'required',
            // 'permission_fields.*.client_field' => 'required',
            // 'permission_fields.*.table_columns' => 'required|array',
        ];

        if($request->permission_fields) {
            $rules['permission_fields.*.title'] = 'required';
            $rules['permission_fields.*.client_field'] = 'required';
            $rules['permission_fields.*.table_columns'] = 'required|array';
        }

        $this->validate($request, $rules);
    }
}
