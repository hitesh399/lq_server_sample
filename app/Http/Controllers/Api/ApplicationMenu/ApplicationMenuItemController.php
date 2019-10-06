<?php

namespace App\Http\Controllers\Api\ApplicationMenu;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ApplicationMenuItem;
use App\Http\Controllers\Controller;
use App\ModelFilters\ApplicationMenuItemFilter;

class ApplicationMenuItemController extends Controller
{
    public function index(Request $request)
    {
        $application_menu_items = ApplicationMenuItem::filter(
            $request->all(),
            ApplicationMenuItemFilter::class
        )->select(
            [
                'name',
                'id',
                'title',
                'menu_order',
                'show_in_menu',
                'description',
                'application_menu_id',
                'permission_ids',
                'parent_id',
            ]
        )->with(
            [
                'permissions' => function ($q) {
                    $q->with('permissionFields');
                },
            ]
        )->orderBy('menu_order')->lqPaginate();

        return $this->setData($application_menu_items)->response();
    }

    public function store(Request $request)
    {
        $this->_validationRule($request);
        $application_menu_item = ApplicationMenuItem::create(
            [
                'name' => $request->name,
                'title' => $request->title,
                'show_in_menu' => $request->show_in_menu,
                'description' => $request->description,
                'application_menu_id' => $request->application_menu,
                'permission_ids' => $request->permissions,
                'parent_id' => $request->parent,
            ]
        );

        return $this->setData([
            'application_menu_item' => $application_menu_item,
        ])->response();
    }

    public function update($id, Request $request)
    {
        $this->_validationRule($request, $id);
        $application_menu_item = ApplicationMenuItem::findOrFail($id);
        $application_menu_item->update(
            [
                'name' => $request->name,
                'title' => $request->title,
                'show_in_menu' => $request->show_in_menu,
                'description' => $request->description,
                'application_menu_id' => $request->application_menu,
                'permission_ids' => $request->permissions,
                'parent_id' => $request->parent,
            ]
        );

        return $this->setData([
            'application_menu_item' => $application_menu_item,
        ])->response();
    }

    public function show($id)
    {
        $application_menu_item = ApplicationMenuItem::findOrFail($id);
        $application_menu_item->load(['parent', 'permissions', 'applicationMenu']);

        return $this->setData([
            'application_menu_item' => $application_menu_item,
        ])->response();
    }

    public function destroy($id)
    {
        $application_menu_item = ApplicationMenuItem::findOrFail($id);
        $application_menu_item->delete();

        return $this->setData([
            'application_menu_item' => $application_menu_item,
        ])->response();
    }

    public function reArrange(Request $request)
    {
        ApplicationMenuItem::batchInsertUpdate($request->data, ['menu_order', 'parent_id']);

        return $this->setMessage('Updated')->response();
    }

    private function _validationRule(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' => ['required', Rule::unique('application_menu_items')->ignore($id)],
            'title' => ['required', 'max:255'],
            'show_in_menu' => ['required', 'in:Yes,No'],
            'description' => ['required'],
            'application_menu' => ['required'],
            'permissions' => ['nullable', 'array'],
            // 'parent' => ['array'],
            // 'menu_order' => 'required'
        ]);
    }
}
