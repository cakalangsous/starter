<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use App\Http\Controllers\AdminController;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Models\TemporaryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class PostController extends AdminController
{
    public function __construct()
    {
        $this->data["parent_menu"] = "posts";
        $this->data["active_menu"] = "posts";
        $this->data['title'] = 'Posts';
    }

    public function index()
    {
        abort_if(!auth()->user()->can('posts_view'), 403);

        return $this->view("posts.index", $this->data);
    }

    function posts_data(Request $request)
    {
        abort_if(!$request->ajax() || !auth()->user()->can('posts_view'), 403);
        
        $data = Post::orderBy("id", "desc")->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn("category", function (Post $post) {
                return $post->category->name;
            })
            ->addColumn("tags", function (Post $post) {
                return $post->tags->map(function ($tag) {
                    return $tag->name;
                })->implode(", ");
            })
            ->addColumn('action', function ($row) {
                $buttons = '';

                if (auth()->user()->can('posts_update')) {
                    $buttons .= '<a class="btn btn-primary me-2 mb-2" title="Edit" href="' . route("admin.posts.edit", ["post" => $row->id]) . '"><i class="bi bi-pencil-fill"></i></a>';
                }

                if (auth()->user()->can('posts_delete')) {
                    $buttons .= '
                    <form method="POST" action="' . route("admin.posts.destroy", ["post" => $row->id]) . '" class="delete-form" id="post_' . $row->id . '_delete_form" data-form_id="post_' . $row->id . '_delete_form"  data-title="' . $row->title . '">
                        <input type="hidden" name="_token" value="' . csrf_token() . '" />
                        <input type="hidden" name="_method" value="DELETE" />
                        <button class="btn btn-danger mb-2 delete-btn" title="Delete"><i class="bi bi-trash-fill"></i></button>
                    </form>
                    ';
                }

                return '
                <div class="d-flex">
                    '.$buttons.'
                </div>
                ';
            })
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        abort_if(!auth()->user()->can('posts_create'), 403);

        $this->data["active_menu"] = "post_create";
        $this->data["categories"] = PostCategory::all();
        $this->data["tags"] = Tag::all();
        $this->data['title'] = 'Create New Post';
        return $this->view("posts.create", $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        abort_if(!auth()->user()->can('posts_create'), 403);

        $temp = TemporaryFile::where("folder", $request->filepond)->first();

        if (!$temp) {
            return redirect()->back()->withErrors("Image is required");
        }

        $data = $request->validated();
        $realpath = "posts/" . $data["slug"] . "/" . $temp->filename;
        Storage::copy("temp/" . $temp->folder . "/" . $temp->filename, $realpath);
        $data["image"] = $realpath;

        $post = Post::create([
            "post_category_id" => $data["category"],
            "user" => auth()->user()->id,
            "slug" => $data["slug"],
            "title" => $data["title"],
            "body" => $data["body"],
            "meta_description" => $data["meta_description"],
            "meta_keywords" => $data["meta_keywords"],
            "excerpt" => $data["excerpt"],
            "publish_status" => $data["publish_status"],
            "published_at" => $data["publish_status"] == 1 ? now() : null,
            "allow_comments" => $request->boolean("allow_comments"),
            "image" => $data["image"]
        ]);

        $post->tags()->attach($data["tags"]);

        Storage::deleteDirectory("temp/" . $temp->folder);
        $temp->delete();

        return redirect(route("admin.posts.index"))->with(["success" => true, "message" => "New post data is saved!"]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        abort_if(!auth()->user()->can('posts_update'), 403);

        $this->data["categories"] = PostCategory::all();
        $this->data["tags"] = Tag::all();
        $this->data["post"] = $post->load(["tags" => function ($query) {
            $query->pluck("tag_id");
        }]);

        $temp = $post->tags->toArray();
        $this->data["tags_selected"] = array_map("current", $temp);
        $this->data['title'] = "Edit $post->title data";
        return $this->view("posts.edit", $this->data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        abort_if(!auth()->user()->can('posts_update'), 403);
        
        $data = $request->validated();
        $temp = TemporaryFile::where("folder", $request->image)->first();
        if ($temp) {
            $realpath = "posts/" . $data["slug"] . "/" . $temp->filename;
            Storage::copy("temp/" . $temp->folder . "/" . $temp->filename, $realpath);
            $data["image"] = $realpath;
        }

        $data["post_category_id"] = $data["category"];
        $data["allow_comments"] = $request->boolean("allow_comments");

        $publish_status = $post->publish_status == "Published" ? 1 : 0;
        if ($data["publish_status"] == 1 && ($data["publish_status"] != $publish_status)) {
            $data["published_at"] = now();
        }
        $post->tags()->sync($data["tags"]);

        unset($data["category"]);
        unset($data["tags"]);

        $post->update($data);


        if ($temp) {
            Storage::deleteDirectory("temp/" . $temp->folder);
            $temp->delete();
        }

        return redirect()->back()->with(["success" => true, "message" => "Post $post->title data is updated!"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        abort_if(!auth()->user()->can('posts_delete'), 403);

        $post->delete();

        return redirect()->back()->with(["success" => true, "message" => "Post Deleted"]);
    }
}
