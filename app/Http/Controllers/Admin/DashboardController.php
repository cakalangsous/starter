<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends AdminController
{
    public function __construct()
    {
        $this->data["active_menu"] = "dashboard";
        $this->data["title"] = "Dashboard";
    }
    function index(): View
    {
        // $role = Role::where('name', 'Super Admin')->first();
        // $role->syncPermissions('roles_view');
        return $this->view('dashboard', $this->data);
    }
}
