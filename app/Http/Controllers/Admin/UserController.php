<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\TemporaryFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends AdminController
{
    public function __construct()
    {
        $this->data["parent_menu"] = "auth";
        $this->data["active_menu"] = "users";
        $this->data['title'] = 'Users';
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        abort_if(!auth()->user()->can('users_view'), 403);

        return $this->view('auth.users.index', $this->data);
    }

    function user_data(Request $request)
    {
        abort_if(!$request->ajax(), 404);

        if (!auth()->user()->can('users_view')) {
            return response()->json(['success' => false, 'message' => 'You\'re not allowed to do this!'], 403);
        }

        $data = User::with('roles')->where("id", "!=", 1)->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn("role", function (User $user) {
                return $user->roles->map(function ($role) {
                    return $role->name;
                })->implode(", ");
            })
            ->addColumn("last_login", function ($row) {
                return $row->last_login ? date('M j, Y H:i', strtotime($row->last_login)) : '-';
            })
            ->addColumn("is_banned", function ($row) {
                $checked = '';

                if ($row->is_banned) {
                    $checked = 'checked';
                }

                return '
                    <div class="form-check form-switch">
                        <input class="form-check-input banned" data-id="'.$row->id.'" data-name="'.$row->name.'" '.$checked.' type="checkbox">
                    </div>
                ';
            })
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('users_update')) {
                    $buttons .= '<a href="'.route('admin.users.edit', ['user' => $row->id]).'" class="btn btn-primary me-2 mb-0" title="Edit"><i class="bi bi-pencil-fill"></i></a>';
                }

                if (auth()->user()->can('users_delete')) {
                    $buttons .= '<a class="btn btn-danger mb-0 delete-data" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete"><i class="bi bi-trash-fill"></i></a>';
                }
                
                return $buttons;
            })
            ->rawColumns(['is_banned', 'action'])
            ->make();
    }

    public function user_ban(Request $req) 
    {
        abort_if(!$req->ajax(), 404);

        if (!auth()->user()->can('users_update')) {
            return response()->json(['success' => false, 'message' => 'You\'re not allowed to do this!']);
        }

        $req->validate(['id' => 'required', 'status' => 'required']);
        abort_if($req->id==1, 404, 'User not found');

        $user = User::whereId($req->id)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unknown user']);
        }

        $user->is_banned = false;
        if ($req->boolean('status')) {
            $user->is_banned = true;
        }

        $user->save();

        $banStatus = $user->is_banned ? 'Ban' : 'Unbanned';
        return response()->json(['success' => true, 'message' => "$banStatus $user->name success!"]);
    }

    public function create() 
    {
        abort_if(!auth()->user()->can('users_create'), 403);

        $this->data['title'] = 'Create User';
        $this->data['roles'] = Role::where('id', '>', 1)->get();
        return $this->view('auth.users.create', $this->data);
    }

    public function store(StoreUserRequest $request)
    {
        abort_if(!auth()->user()->can('users_create'), 403);
        
        $temp = TemporaryFile::where("folder", $request->filepond)->first();
        
        if (!$temp) {
            return redirect()->back()->withInput()->withErrors("Avatar is required");
        }
        
        $data = $request->validated();
        $realpath = "admin/" . $temp->filename;
        Storage::copy("temp/" . $temp->folder . "/" . $temp->filename, $realpath);
        $data['avatar'] = $realpath;

        $data['password'] = bcrypt($data['password']);
        $newUser = User::create($data);

        $newUser->syncRoles($data['roles']);
        
        return redirect(route('admin.users.index'))->with(['success' => true, 'message' => 'New user data is saved!']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        abort_if(!auth()->user()->can('users_update'), 403);

        abort_if($user->id==1 && auth()->user()->id != 1, 404);
        $this->data['title'] = 'Edit '.$user->name.' data';
        $this->data['user'] = $user;
        $roles = [];

        foreach ($user->roles as $role) {
            array_push($roles, $role->id);
        }

        $this->data['roles'] = Role::where('id', '>', 1)->get();
        $this->data['user_roles'] = $roles;
        return $this->view('auth.users.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request, User $user)
    {
        abort_if(!auth()->user()->can('users_update'), 403);

        abort_if($user->id==1 && auth()->user()->id != 1, 404);
        
        $data = $request->validated();

        $temp = TemporaryFile::where("folder", $request->filepond)->first();
        
        if ($temp) {
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
    
            $realpath = "admin/" . $temp->filename;
            Storage::copy("temp/" . $temp->folder . "/" . $temp->filename, $realpath);
            $data['avatar'] = $realpath;
        }
        

        if ($data['password'] == null) {
            unset($data['password']);
            unset($data['password_confirmation']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        if ($temp) {
            File::deleteDirectory(public_path("storage/temp/" . $temp->folder));
            $temp->delete();
        }
        
        if (!$user->hasRole('Developer')) {
            $user->syncRoles($data['roles']);
        }

        return redirect(route('admin.users.index'))->with(['success' => true, 'message' => $user->name.' data updated!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        abort_if(!$request->ajax(), 404);

        if (!auth()->user()->can('users_delete')) {
            return response()->json(['success' => false, 'message' => 'You are not allowed to do this!'], 403);
        }

        if (auth()->user()->id == $user->id) {
            return response()->json(['success' => false, 'message' => 'You\'re not allowed to delete your account!']);
        }

        if (!$request->ajax() || $user->id == 1) {
            return response()->json(['success' => false, 'message' => 'Unknown data!']);
        }

        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        foreach ($user->getRoleNames() as $role) {
            $user->removeRole($role);
        }
        $user->delete();

        return response()->json(['success' => true, 'message' => "$user->name is deleted!"]);
    }
}
