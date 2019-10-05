<?php

namespace App\Http\Controllers\Api\ApplicationMenu;

use Illuminate\Http\Request;
use App\Models\ApplicationMenu;
use App\Http\Controllers\Controller;
use App\ModelFilters\ApplicationMenuFilter;

class ApplicationMenuController extends Controller
{
    public function index(Request $request)
    {
        $application_menu = ApplicationMenu::filter(
            $request->all(),
            ApplicationMenuFilter::class
        )->select('id', 'name', 'client_ids', 'role_ids')
            ->with(
                [
                    'clients',
                    'roles',
                ]
        )->lqPaginate();
        return $this->setData($application_menu)->response();
    }
    public function store(Request $request)
    {
        $this->_validationRule($request);
        $application_menu = ApplicationMenu::create([
            'name' => $request->name,
            'client_ids' => $request->clients,
            'role_ids' => $request->roles,
        ]);
        return $this->setData([
            'application_menu' => $application_menu
        ])->response();
    }
    public function update($id, Request $request)
    {
        $this->_validationRule($request, $id);
        $application_menu = ApplicationMenu::findOrFail($id);
        $application_menu->update([
            'name' => $request->name,
            'client_ids' => $request->clients,
            'role_ids' => $request->roles,
        ]);
        return $this->setData([
            'application_menu' => $application_menu
        ])->response();
    }
    public function show($id) {
        $application_menu = ApplicationMenu::findOrFail($id);
        $application_menu->load(['clients', 'roles']);

        return $this->setData([
            'application_menu' => $application_menu
        ])->response();
    }
    public function destroy($id)
    {
        $application_menu = ApplicationMenu::findOrFail($id);
        $application_menu->delete();
        return $this->setData([
            'application_menu' => $application_menu
        ])->response();
    }
    private function _validationRule(Request $request, $id = null)
    {
        $this->validate($request, [
            'name' => 'required',
            'clients' => 'required'
        ]);
    }
}
