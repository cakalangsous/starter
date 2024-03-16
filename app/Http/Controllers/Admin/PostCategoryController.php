<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StorePostCategoryRequest;
use App\Http\Requests\Admin\UpdatePostCategoryRequest;
use App\Models\PostCategory;
use App\Http\Controllers\AdminController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PostCategoryController extends AdminController
{
    public function __construct()
    {
        $this->data["parent_menu"] = 'posts';
        $this->data["active_menu"] = "post_categories";
        $this->data['title'] ='Post Category';

    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        abort_if(!auth()->user()->can('post_categories_view'), 403);
        
        return $this->view("posts_categories.index", $this->data);
    }

    function post_categories_data(Request $request)
    {
        abort_if(!$request->ajax() || !auth()->user()->can('post_categories_view'), 403);
        
        $data = PostCategory::orderBy("id", "desc")->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('post_categories_update')) {
                    $buttons .= '<a class="btn btn-primary me-2 mb-2 edit-post-category" data-id="' . $row->id . '" data-name="' . $row->name . '" data-slug="' . $row->slug . '" data-description="' . $row->description . '" title="Edit"><i class="bi bi-pencil-fill"></i></a>';
                }

                if (auth()->user()->can('post_categories_delete')) {
                    $buttons .= '<a class="btn btn-danger mb-2 delete-post-category" data-id="' . $row->id . '" data-name="' . $row->name . '" title="Delete"><i class="bi bi-trash-fill"></i></a>';
                }

                return $buttons;
            })
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostCategoryRequest $request): RedirectResponse
    {
        abort_if(!auth()->user()->can('post_categories_create'), 403);
        
        PostCategory::create($request->validated());

        return redirect(route("admin.post_categories.index"))->with(["success" => true, "message" => "New Category Stored."]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $postCategory): RedirectResponse
    {
        abort_if(!auth()->user()->can('post_categories_update'), 403);

        $validated = $request->validated();
        $postCategory->name = $validated["name"];
        $postCategory->slug = $validated["slug"];
        $postCategory->description = $validated["description"];

        $postCategory->save();

        return redirect(route("admin.post_categories.index"))->with(["success" => true, "message" => "Post Category Updated"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostCategory $postCategory): JsonResponse
    {
        abort_if(!auth()->user()->can('post_categories_delete'), 403);

        $postCategory->delete();

        return response()->json(["success" => true, "message" => "Post Category Deleted."]);
    }
}
