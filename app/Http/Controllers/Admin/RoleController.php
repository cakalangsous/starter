<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends AdminController
{

    public function __construct() {
        $this->data["parent_menu"] = "auth";
        $this->data["active_menu"] = "roles";
        $this->data['title'] = 'Roles';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!auth()->user()->can('roles_view'), 403);

        $this->data['roles'] = Role::where('id', '>', 1)->get();
        return $this->view('auth.roles.index', $this->data);
    }

    function roles_data(Request $request)
    {
        abort_if(!$request->ajax() || !auth()->user()->can('roles_view'), 403);

        $data = Role::where('id', '>', 1)->orderBy("id", "desc")->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $buttons = '';
                if (auth()->user()->can('roles_update')) {
                    $buttons .= '<a class="btn btn-primary me-2 mb-0 edit-data" data-id="' . $row->id . '" data-name="' . $row->name . '"  title="Edit"><i class="bi bi-pencil-fill"></i></a>';
                }

                if (auth()->user()->can('roles_delete')) {
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
        abort_if(!auth()->user()->can('roles_create'), 403);
        $role = $request->validate(['name' => 'required|max:250']);

        Role::create($role);
        return back()->with(['success' => true, 'message' => 'New Role added successfully!']);
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
        abort_if(!auth()->user()->can('roles_update'), 403);

        $request->validate(['name' => 'required|max:250']);

        $role = Role::findOrFail($id);

        $role->name = $request->name;
        $role->save();

        return redirect(route('admin.roles.index'))->with(['success' => true, 'message' => 'Role successfully updated!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if (!auth()->user()->can('permissions_delete')) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to do this!'], 403);
        }

        $temp = $role;
        Role::destroy($role->id);
        return response()->json(['success' => true, 'message' => 'Role "'.$temp->name.'" successfully deleted!']);
    }
}
