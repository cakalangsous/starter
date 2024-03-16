<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessController extends AdminController
{

    public function __construct() {
        $this->data["parent_menu"] = "auth";
        $this->data["active_menu"] = "access";
        $this->data['title'] = "Access";
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('access_view'), 403);
        $this->data['title'] = 'Access';
        $this->data['perms'] = Permission::latest()->get();
        $this->data['roles'] = Role::where('id', '>', 1)->latest()->get();
        return $this->view('auth.access.index', $this->data);
    }

    public function getAccessByRole(Request $req)
    {
        abort_if(!$req->ajax() || !auth()->user()->can('access_view'), 403);

        $req->validate(['role' => 'required']);

        $role = Role::findOrFail($req->role);

        $role_perms = $role->permissions;
        $perms = Permission::all();

        $group_perms_grouping = [];
        foreach ($perms as $perm) {
            $group_name = '';
            $perm_tmp_arr = explode('_', $perm->name);

            if (isset($perm_tmp_arr[0]) AND !empty($perm_tmp_arr[0])) {
                $group_name =  strtolower($perm_tmp_arr[0]);
            }

            if ($perm->table_name!=NULL) {
                $group_name =  strtolower($perm->table_name);
            }

            $group_perms_grouping[$group_name][] = $perm;
        }

        return response()->json(['success' => true, 'message' => "Get $role->name permissions success!", 'perms' => $group_perms_grouping, 'role_perms' => $role_perms]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $role)
    {
        abort_if(!auth()->user()->can('access_update'), 403);

        $roleData = Role::findOrFail($role);

        $roleData->syncPermissions($request->perms);

        Artisan::call('optimize:clear');
        return redirect(route('admin.access.index'))->with(['success' => true, 'message' => "Permissions for $roleData->name are updated!"]);
    }

}
