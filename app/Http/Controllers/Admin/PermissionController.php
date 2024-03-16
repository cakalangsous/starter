<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionController extends AdminController
{

    private $tables = [
        "commentables",
        "crud_input_types",
        "crud_input_validations",
        "failed_jobs",
        "migrations",
        "model_has_permissions",
        "model_has_roles",
        "password_reset_tokens",
        "personal_access_tokens",
        "role_has_permissions",
        "taggables",
        "temporary_files",
    ];


    public function __construct() {

        $this->data["parent_menu"] = "auth";
        $this->data["active_menu"] = "permissions";
        $this->data['title'] = 'Permissions';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('permissions_view'), 403);
        
        $this->data['permissions'] = Permission::orderByDesc('id')->get();
        $tables = DB::select('SHOW TABLES');
        $tables = array_map('current',$tables);
        $this->data['tables'] = array_diff($tables, $this->tables);
        return $this->view('auth.permissions.index', $this->data);
    }

    function permissions_data(Request $request)
    {
        abort_if(!$request->ajax() || !auth()->user()->can('permissions_view'), 403);
        
        $data = Permission::orderByDesc("id")->get();
        
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('permissions_update')) {
                    $buttons .= '<a class="btn btn-primary me-2 mb-0 edit-data" data-id="' . $row->id . '" data-name="' . $row->name . '" data-table='.$row->table_name.' title="Edit"><i class="bi bi-pencil-fill"></i></a> ';
                }

                if (auth()->user()->can('permissions_delete')) {
                    $buttons .= '<a class="btn btn-danger mb-0 delete-data" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete"><i class="bi bi-trash-fill"></i></a>';
                }
                
                return $buttons;
            })
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        abort_if(!auth()->user()->can('permissions_create'), 403);

        $permission = $request->validate(['name' => 'required|max:250', 'table_name' => 'required']);

        Permission::create($permission);
        return back()->with(['success' => true, 'message' => 'New Permisssion data is saved!']);
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
        abort_if(!auth()->user()->can('permissions_update'), 403);

        $data = $request->validate(['name' => 'required|max:250', 'table_name' => 'required']);

        $permission = Permission::findOrFail($id);

        $permission->update($data);

        return redirect(route('admin.permissions.index'))->with(['success' => true, 'message' => 'Permisssion data is updated!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        if (!auth()->user()->can('permissions_delete')) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to do this!'], 403);
        }

        $temp = $permission;
        Permission::destroy($permission->id);
        return response()->json(['success' => true, 'message' => 'Role "'.$temp->name.'" successfully deleted!']);
    }
}
