<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\Tag;
use App\Http\Controllers\AdminController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class TagController extends AdminController
{
    public function __construct()
    {
        $this->data["parent_menu"] = "posts";
        $this->data["active_menu"] = "tags";
        $this->data['title'] = "Tags";
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return $this->view("tags.index", $this->data);
    }

    function tags_data(Request $request)
    {
        if ($request->ajax()) {
            $data = Tag::orderBy("id", "desc")->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a class="btn btn-primary me-2 mb-2 edit-post-category" data-id="' . $row->id . '" data-name="' . $row->name . '" data-slug="' . $row->slug . '" data-description="' . $row->description . '" title="Edit"><i class="bi bi-pencil-fill"></i></a> <a class="btn btn-danger mb-2 delete-post-category" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete"><i class="bi bi-trash-fill"></i></a>';
                })
                ->make();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());

        return redirect(route("admin.tags.index"))->with(["success" => true, "message" => "New Tag Stored."]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validated();
        $tag->name = $validated["name"];
        $tag->slug = $validated["slug"];
        $tag->description = $validated["description"];

        $tag->save();

        return redirect(route("admin.tags.index"))->with(["success" => true, "message" => "Tag Updated"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json(["success" => "false", "message" => "Tag Deleted."]);
    }
}
